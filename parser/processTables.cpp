#include <vector>
#include <string>
#include <iostream>
#include "readFile.h"
#include "JSONWriter.h"

using namespace std;

typedef struct
{
  unsigned y;
  unsigned x1;
  unsigned x2;
} header;

typedef struct
{
  unsigned x1;
  unsigned y1;
  unsigned x2;
  unsigned y2;
} table;

vector<header> detectRows( vector< vector<sheetNode> > spreadsheet )
{
  unsigned currentPos;
  unsigned stringCount;
  unsigned height = 0;
  vector<header> headers;
  header newHeader;
  for(
    vector< vector<sheetNode> >::iterator outIt = spreadsheet.begin();
    outIt != spreadsheet.end();
    outIt++
  )
  {
    currentPos = 0;
    stringCount = 0;
    for(
      vector<sheetNode>::iterator inIt = outIt->begin();
      inIt != outIt->end();
      inIt++
    )
    {
      if( inIt->getType() == STRING )
      {
        stringCount++;
      }
      else
      {
        if( stringCount > 1 )
        {
          newHeader.y = height;
          newHeader.x1 = currentPos - stringCount;
          newHeader.x2 = currentPos - 1;
          headers.push_back( newHeader );
        }
        stringCount = 0;
      }
      currentPos++;
    }
    if( stringCount > 1 )
    {
      newHeader.x2 = currentPos - 1;
      newHeader.x1 = currentPos - 1 - stringCount ;
      headers.push_back( newHeader );
    }
    height++;
  }
  return headers;
}

bool containedIn( vector<table> tables, header row )
{
  //Assumes that the row is always at the same height or lower on the
  //spreadsheet than the lowest table in tables
  //This is the case in intended use as tables are added to the list in
  //vertical descending order and the row always represents the latest table
  for( vector<table>::iterator it = tables.begin();
    it != tables.end();
    it++ )
  {
    if( ( row.x1 >= it->x1 ) && ( row.x2 <= it->x2 )  )
    {
      if( row.y <= it->y2 )
      {
        return true;
      }
    }
  }
  return false;
}

int area( table square )
{
  int size = ( square.x2 + 1 - square.x1 ) * ( square.y2 + 1 - square.y1 );
  return size;
}

void printBoolGrid( vector< vector<bool> > grid )
{
  for( vector< vector<bool> >::iterator outIt = grid.begin();
    outIt != grid.end();
    outIt++ )
  {
    for( vector<bool>::iterator inIt = outIt->begin();
      inIt != outIt->end();
      inIt++ )
    {
      cout << *inIt << " ";
    }
    cout << '\n';
  }
  cout << '\n';
}

table getTable( vector< vector<sheetNode> > spreadsheet, header row )
{
  table newTable;
  table maxTable;
  maxTable.x1 = 0;
  maxTable.x2 = 0;
  maxTable.y1 = 0;
  maxTable.y2 = 0;
  unsigned cX;
  unsigned cY;
  bool blank;
  vector< vector<bool> > checkedCells;
  vector<bool> newRow( row.x2 - row.x1 + 1, false );
  for( unsigned count = row.x1; count <= row.x2; count++ )
  {
    blank = false;
    cY = row.y;
    newTable.x1 = count;
    newTable.y1 = cY;
    while( !blank )
    {
      cY++;
      if( cY - row.y > checkedCells.size() )
      {
        checkedCells.push_back( newRow );
      }
      if( spreadsheet[cY][count].getType() == NULLVALUE )
      {
        blank = true;
      }
    }
    for( unsigned count2 = cY-1; count2 > row.y; count2-- )
    {
      cX = count;
      if( !checkedCells[count2 - row.y - 1][cX - row.x1] )
      {
        checkedCells[count2 - row.y - 1][cX - row.x1] = true;
        blank = false;
        while( !blank )
        {
          cX++;
          checkedCells[count2 - row.y - 1][cX - row.x1] = true;
          if( spreadsheet[count2][cX].getType() == NULLVALUE )
          {
            blank = true;
          }
        }
        newTable.x2 = cX-1;
        newTable.y2 = count2;
        if( area(newTable) > area(maxTable) )
        {
          maxTable = newTable;
        }
      }
    }
  }
  return maxTable;
}

bool collisionTables( table first, table second )
{
  if( first.x2 < second.x1 || first.x1 > second.x2 )
  {
    return false;
  }
  if( first.y2 < second.y1 || first.y1 > second.y2 )
  {
    return false;
  }
  return true;
}

void collisionScan( vector<table> &tables )
{
  for( vector<table>::iterator outIt = tables.begin();
    outIt != tables.end();
    outIt++ )
  {
    for( vector<table>::iterator inIt = outIt + 1;
      inIt != tables.end();
      inIt++ )
    {
      if( collisionTables( *outIt, *inIt ) )
      {
        if( area( *inIt ) <= area( *outIt ) )
        {
            inIt = tables.erase( inIt );
            inIt--;
        }
        else
        {
          outIt = tables.erase( outIt );
          outIt--;
          break;
        }
      }
    }
  }
}

vector<table> contentScan( vector< vector<sheetNode> > spreadsheet, vector<header> headers )
{
  vector<table> tables;
  table newTable;
  for( vector<header>::iterator it = headers.begin();
    it != headers.end();
    it++ )
  {
    if( !containedIn( tables, *it ) )
    {
      newTable = getTable( spreadsheet, *it );
      if( newTable.y2 - newTable.y1 > 1 )
      {
        tables.push_back( newTable );
      }
    }
  }
  return tables;
}

//Only clears spaces and tabs
string clearWhiteSpace( string target )
{
  size_t start = 0;
  size_t end = target.size() - 1;
  while( target[start] == ' ' || target[start] == '\t' ) start++;
  while( target[end] == ' ' || target[end] == '\t' ) end--;
  if( start != 0 || end != target.size() - 1 )
  {
    if( start >= end || end == string::npos ) return "";
    string result = target;
    cout << '"' << result << "\", " << start << ", " << end << '\n';
    result.resize( end + 1 );
    return result.substr( start );
  }
  return target;
}

vector<JSONObject> encodeTable(
  vector< vector<sheetNode> > spreadsheet, table data )
{
  unsigned cX;
  unsigned cY = data.y1 + 1;
  string nameval;
  string strval;
  long double numval;
  bool boolval;
  JType datatype;
  vector<JSONObject> result;
  while( cY <= data.y2 )
  {
    JSONObject next;
    cX = data.x1;
    while( cX <= data.x2 )
    {
      nameval = spreadsheet[data.y1][cX].getString();
      datatype = spreadsheet[cY][cX].getType();
      switch( datatype )
      {
        case STRING:
          strval = spreadsheet[cY][cX].getString();
          strval = clearWhiteSpace( strval );
          strval = "\"" + strval + "\"";
          next.addPair( nameval, strval );
          break;
        case NUMBER:
          numval = spreadsheet[cY][cX].getNumber();
          next.addPair( nameval, numval );
          break;
        case OBJECT:
        case ARRAY:
          break;
        case BOOL:
          boolval = spreadsheet[cY][cX].getBool();
          next.addPair( nameval, boolval );
          break;
        case NULLVALUE:
          next.addPair( nameval );
          break;
      }
      cX++;
    }
    result.push_back( next );
    cY++;
  }
  return result;
}

vector< vector<JSONObject> > encodeTables(
  vector< vector<sheetNode> > spreadsheet, vector<table> tables )
{
  vector< vector<JSONObject> > result;
  for( vector<table>::iterator it = tables.begin();
    it != tables.end();
    it++ )
  {
    vector<JSONObject> nextObject( encodeTable( spreadsheet, *it ) );
    result.push_back( nextObject );
  }
  return result;
}

vector< vector<JSONObject> > processData( vector< vector<sheetNode> > spreadsheet )
{
  //Padding the spreadsheet to prevent out-of-bounds access errors during
  //table scanning
  sheetNode nullcell;
  vector<sheetNode> nullrow( spreadsheet[0].size()+1, nullcell );
  for( vector< vector<sheetNode> >::iterator it = spreadsheet.begin();
    it != spreadsheet.end();
    it++ )
  {
    it->push_back( nullcell );
  }
  spreadsheet.push_back( nullrow );
  vector<header> initialHeaders( detectRows( spreadsheet ) );
  vector<table> initialTables( contentScan( spreadsheet, initialHeaders ) );
  collisionScan( initialTables );
  vector< vector<JSONObject> > result( encodeTables( spreadsheet, initialTables ) );
  return result;
}
