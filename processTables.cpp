#include <vector>
#include <string>
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
          newHeader.x2 = currentPos;
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
  int size = ( square.x2 - square.x1 ) * ( square.y2 - square.y1 );
  return size;
}

table getTable( vector< vector<sheetNode> > spreadsheet, header row )
{
  table result;
  //Magic happens...
  return result;

  /*table newTable;
  table maxTable;
  maxTable.x1 = 0;
  maxTable.x2 = 0;
  maxTable.y1 = 0;
  maxTable.y2 = 0;
  unsigned cX;
  unsigned cY;
  bool blank;
  for( int count = row.x1; count <= row.x2; count++ )
  {
    blank = false;
    cX = count;
    cY = row.y;
    newTable.x1 = cX;
    newTable.y1 = cY;
    while( !blank )
    {
      cY++;
      if( spreadsheet[cY][cX].getType() == NULLTYPE )
      {
        blank = true;
      }
    }
    cY--;
    blank = false;
    while( !blank )
    {
      cX++;
      if( spreadsheet[cY][cX].getType() == NULLTYPE )
      {
        blank = true;
      }
    }
    cX--;
    newTable.x2 = cX;
    newTable.y2 = cY;
    if( area(newTable) < area(maxTable) )
    {
      maxTable = newTable;
    }
  }*/
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

vector<JSONObject> encodeTable(
  vector< vector<sheetNode> > spreadsheet, table data )
{
  unsigned cX;
  unsigned cY = data.y1 + 1;
  string nameval;
  string strval;
  double numval;
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
          next.addPair( nameval, strval );
          break;
        case NUMBER:
          numval = spreadsheet[cY][cX].getNumber();
          next.addPair( nameval, numval );
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
