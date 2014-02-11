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
    cout << "\n";
  }
  cout << "\n";
}

table getTable( vector< vector<sheetNode> > spreadsheet, header row )
{
  //Test code commented out
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
  for( int count = row.x1; count <= row.x2; count++ )
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
        //printBoolGrid( checkedCells );
      }
      if( spreadsheet[cY][count].getType() == NULLVALUE )
      {
        blank = true;
      }
    }
    for( int count2 = cY-1; count2 > row.y; count2-- )
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
        //cout << "New table (" << newTable.x1 << "," << newTable.y1 << "),("
        //  << newTable.x2 << "," << newTable.y2 << ") found with area "
        //  << area(newTable) << "..." << "\n";
        if( area(newTable) > area(maxTable) )
        {
          //cout << "New Max-Table found." << "\n";
          maxTable = newTable;
        }
        //printBoolGrid( checkedCells );
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
  //cout << "\t\t" << "Beginning scanning testing protocols..." << "\n";
  vector<table> tables;
  table newTable;
  for( vector<header>::iterator it = headers.begin();
    it != headers.end();
    it++ )
  {
    if( !containedIn( tables, *it ) )
    {
      //cout << "\t\t" << "Getting table from row at " << "(" << it->x1 << ":"
      //  << it->x2 << "," << it->y << ")..." << "\n";
      newTable = getTable( spreadsheet, *it );
      //cout << "\t\t" << "Table has coords " << "(" << newTable.x1 << "," <<
      //  newTable.y1 << "),(" << newTable.x2 << "," << newTable.y2 << ")."
      //  << "\n";
      if( newTable.y2 - newTable.y1 > 1 )
      {
        //cout << "\t\t" << "Table added." << "\n";
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

vector< vector<JSONObject> > processData( vector< vector<sheetNode> > spreadsheet )
{
  //Commented out testing code
  //Padding the spreadsheet to prevent out-of-bounds access errors during
  //table scanning
  //cout << "\t" << "Beginning processing testing protocols..." << "\n";
  sheetNode nullcell;
  vector<sheetNode> nullrow( spreadsheet[0].size()+1, nullcell );
  //cout << "\t" << "Padding spreadsheet..." << "\n";
  for( vector< vector<sheetNode> >::iterator it = spreadsheet.begin();
    it != spreadsheet.end();
    it++ )
  {
    it->push_back( nullcell );
  }
  spreadsheet.push_back( nullrow );
  //cout << "\t" << "Detecting rows..." << "\n";
  vector<header> initialHeaders( detectRows( spreadsheet ) );
  //cout << "\t" << initialHeaders.size() << " rows detected." << "\n";
  /*
  for( vector<header>::iterator it = initialHeaders.begin();
    it != initialHeaders.end();
    it++ )
  {
    cout << "\t\t" << "(" << it->x1 << ":" << it->x2 << ","
      << it->y << ")" << "\n";
  }
  */
  //cout << "\t" << "Scanning rows for content..." << "\n";
  vector<table> initialTables( contentScan( spreadsheet, initialHeaders ) );
  //cout << "\t" << "Scanning for collisions..." << "\n";
  collisionScan( initialTables );
  //cout << "\t" << "Encoding tables..." << "\n";
  vector< vector<JSONObject> > result( encodeTables( spreadsheet, initialTables ) );
  //cout << "\t" << "Process complete." << "\n";
  return result;
}

/*
int main()
{
  cout << "Initiating testing protocols..." << "\n";
  cout << "Creating sample spreadsheet..." << "\n";
  vector< vector<sheetNode> > spreadsheet;
  string blah( "blah" );
  sheetNode nullcell;
  sheetNode strcell( blah );
  sheetNode numcell( 42.0 );
  sheetNode boolcell( true );
  
  vector<sheetNode> row1;
  for( int count=0; count<7; count++ )
  {
    row1.push_back( nullcell );
  }
  vector<sheetNode> row2;
  for( int count=0; count<7; count++ )
  {
    row2.push_back( strcell );
  }
  vector<sheetNode> row3;
  row3.push_back( nullcell );
  for( int count=0; count<6; count++ )
  {
    row3.push_back( strcell );
  }
  vector<sheetNode> row4;
  row4.push_back( nullcell );
  for( int count=0; count<6; count++ )
  {
    row4.push_back( numcell );
  }
  vector<sheetNode> row5( row3 );
  vector<sheetNode> row6;
  row6.push_back( nullcell );
  row6.push_back( strcell );
  row6.push_back( strcell );
  row6.push_back( nullcell );
  row6.push_back( strcell );
  row6.push_back( nullcell );
  row6.push_back( nullcell );
  vector<sheetNode> row7;
  row7.push_back( nullcell );
  row7.push_back( nullcell );
  row7.push_back( boolcell );
  for( int count=0; count<4; count++ )
  {
    row7.push_back( nullcell );
  }
  vector<sheetNode> row8( row7 );
  vector<sheetNode> row9( row7 );
  
  spreadsheet.push_back( row1 );
  spreadsheet.push_back( row2 );
  spreadsheet.push_back( row3 );
  spreadsheet.push_back( row4 );
  spreadsheet.push_back( row5 );
  spreadsheet.push_back( row6 );
  spreadsheet.push_back( row7 );
  spreadsheet.push_back( row8 );
  spreadsheet.push_back( row9 );
  
  for(
    vector< vector<sheetNode> >::iterator it1 = spreadsheet.begin();
    it1 != spreadsheet.end();
    it1++
  )
  {
    for(
      vector<sheetNode>::iterator it2 = it1->begin();
      it2 != it1->end();
      it2++
    )
    {
      if( it2->getType() == STRING )
      {
        cout << it2->getString() << "  ";
      }
      else if( it2->getType() == NUMBER )
      {
        cout << it2->getNumber() << "  ";
      }
      else if( it2->getType() == BOOL )
      {
        cout << it2->getBool() << "  ";
      }
      else if( it2->getType() == NULLVALUE )
      {
        cout << "NULL" << "  ";
      }
      else
      {
        cout << "UNKNOWN ";
      }
    }
    cout << "\n";
  }
  cout << "\n----------------------------------------\n";

  cout << "Sample spreadsheet created." << "\n";
  cout << "Beginning data processing..." << "\n";
  vector< vector<JSONObject> > result( processData( spreadsheet ) );
  cout << "Processing complete." << "\n";
  cout << "\n";
  int fieldCount;
  for( vector< vector<JSONObject> >::iterator outIt = result.begin();
    outIt != result.end();
    outIt++ )
  {
    cout << "Printing table..." << "\n";
    for( vector<JSONObject>::iterator inIt = outIt->begin();
      inIt != outIt->end();
      inIt++ )
    {
      fieldCount = inIt->fieldCount();
      for( int count = 0; count < fieldCount; count++ )
      {
        cout << inIt->getName(count) << ":" << inIt->getValue(count) << "\n";
      }
      cout << "\n";
    }
    cout << "\n";
  }
  cout << "Test complete." << "\n";
}
*/
