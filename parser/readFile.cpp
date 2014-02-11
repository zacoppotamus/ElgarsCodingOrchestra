#include <iostream>
#include <fstream>
#include <string>
#include <sstream>
#include <vector>
#include "readFile.h"

#define MAX_STRING_SIZE 256

/////////////////////////////////////////////////////////////////
/////////  sheetNode Class implementation
sheetNode::sheetNode()
{
  jType = NULLVALUE;
}

sheetNode::sheetNode( string newString )
{
  jType = STRING;
  strval = newString;
}

sheetNode::sheetNode( double newNumber )
{
  jType = NUMBER;
  numval = newNumber;
}

sheetNode::sheetNode( bool newBool )
{
  jType = BOOL;
  boolval = newBool;
}

string sheetNode::getString()
{
  if( jType != STRING )
    return "";
  else
    return strval;
}

double sheetNode::getNumber()
{
  if( jType != NUMBER )
    return 0;
  else
    return numval;
}

bool sheetNode::getBool()
{
  if( jType != BOOL )
    return false;
  else
    return boolval;
}

JType sheetNode::getType()
{
  return jType;
}
/////////  End of implementation
/////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////
/////////  Utility functions
string simpleToUpper( string input )
{
  unsigned length = input.length();
  string upper = input;
  for( int i = 0; i<length; i++ )
    if( upper[i] > 96 && upper[i] < 123 )
      upper[i] -= 32;
  return upper;
}

FileType getType( string fileName )
{
  unsigned marker = fileName.find_last_of('.');
  string fileStr = fileName.substr(marker+1);
  fileStr = simpleToUpper( fileStr );
  if( fileStr.compare( "CSV" ) == 0 )
    return CSV;
  else
    return UNDEF;
}
/////////  End of utility functions
/////////////////////////////////////////////////////////////////

unsigned charsUntil( ifstream &input, char target, char delim )
{
  int startPosition = input.tellg();
  unsigned count = 0;
  char next;
  while( !input.eof() )
  {
    next = input.get();
    if( next == delim )
    {
      input.seekg( startPosition );
      return count;
    }
    else if( next == '"' )
    {
      next = 0;
      while ( next != '"' )
      {
        if( input.eof() )
        {
          input.seekg( startPosition );
          return count;
        }
        next = input.get();
      }
    }
    else if( next == target )
    {
      count++;
    }
  }
  input.clear();
  input.seekg( startPosition );
  return count;
}

unsigned charsUntil( ifstream &input, char target )
{
  int startPosition = input.tellg();
  unsigned count = 0;
  char next;
  while( !input.eof() )
  {
    next = input.get();
    if( next == '"' )
    {
      next = 0;
      while ( next != '"' )
      {
        if( input.eof() )
        {
          input.seekg( startPosition );
          return count;
        }
        next = input.get();
      }
    }
    else if( next == target )
    {
      count++;
    }
  }
  input.clear();
  input.seekg( startPosition );
  return count;
}

string getCSV( ifstream &input )
{
  //Test code commented out
  string result = "";
  char next = 0;
  bool quotes = false;
  //cout << "_____" << "Initiating CSV-get tests..." << "\n";
  if( !input.good() )
  {
    //cout << "_____" << "Bad filestream." << "\n";
    return "";
  }
  while( !input.eof() )
  {
    next = input.get();
    if( next == '"' )
    {
      quotes = !quotes;
    }
    else if( !quotes )
    {
      if( next == ',' || next == '\n' )
      {
        return result;
      }
    }
    result = result + next;
    //cout << "______" << "Next = """ << next
    //  << """, Current result: """ << result << """" << "\n";
  }
  return result;
}

void insertValue( string csvalue, vector<sheetNode> &cell )
{
  if( csvalue.compare( "" ) == 0 )
  {
    sheetNode newsheet;
    cell.push_back( newsheet );
    return;
  }
  string upper = simpleToUpper( csvalue );
  if( upper.compare( "TRUE" ) == 0 )
  {
    sheetNode newsheet( true );
    cell.push_back( newsheet );
    return;
  }
  else if( upper.compare( "FALSE" ) == 0 )
  {
    sheetNode newsheet( false );
    cell.push_back( newsheet );
    return;
  }
  else
  {
    stringstream conversion( csvalue );
    double numval;
    if( ( conversion >> numval ) )
    {
      sheetNode newsheet( numval );
      cell.push_back( newsheet );
      return;
    }
    else
    {
      sheetNode newsheet( csvalue );
      cell.push_back( newsheet );
      return;
    }
  }
}

vector< vector<sheetNode> > readCSV( ifstream &input )
{
  //Test code commented out
  //cout << "__" << "Initiating CSV reading tests..." << "\n";
  unsigned width = 0;
  unsigned height = 0;
  bool accept = false;
  if( !input.good() )
  {
    //cout << "__" << "Bad filestream." << "\n";
    vector< vector<sheetNode> > failure;
    return failure;
  }
  width = charsUntil( input, ',', '\n' ) + 1;
  height = charsUntil( input, '\n' );
  //cout << "__" << "Character counts obtained..." << "\n";
  vector<sheetNode> blankvector;
  vector< vector<sheetNode> > spreadsheet ( height, blankvector );
  //cout << "__" << "Vector generated..." << "\n";
  unsigned cHeight = 0;
  string csvalue;
  JType valueType;
  //cout << "__" << "Beginning population loop..." << "\n";
  while( cHeight < height )
  {
    //cout << "___" << "Outer loop iteration " << cHeight << "." << "\n";
    unsigned cWidth = 0;
    while( cWidth < width )
    {
      //cout << "____" << "Inner loop iteration " << cWidth << "." << "\n";
      csvalue = getCSV( input );
      //cout << "____" << "CSValue obtained." << "\n";
      insertValue( csvalue, spreadsheet[cHeight] );
      //cout << "____" << "CSValue \"" << csvalue << "\" of type "
      // << spreadsheet[cHeight][cWidth].getType() << " inserted." << "\n";
      cWidth++;
    }
    cHeight++;
  }
  //cout << "__" << "Spreadsheet populated..." << "\n";
  return spreadsheet;
}

vector< vector<sheetNode> > getFile( string fileName )
{
  //Test code commented out
  //cout << "_" << "Testing file access on " << fileName << "..." << "\n";
  FileType filetype = getType( fileName );
  //cout << "_" << "Filetype " << filetype << " detected." << "\n";
  ifstream input( fileName );
  //cout << "_" << "File opened..." << "\n";
  if( !input.good() )
  {
    //cout << "_" << "File not opened successfully." << "\n";
    vector< vector<sheetNode> > failure;
    return failure;
  }
  if( filetype == CSV )
  {
    //cout << "_" << "Reading CSV file..." << "\n";
    return readCSV( input );
  }
  else if( filetype == UNDEF )
  {
    //cout << "_" << "Undefined file type." << "\n";
    vector< vector<sheetNode> > failure;
    return failure;
  }
}

/*
int main()
{
  cout << "Initiating testing protocols..." << "\n";
  vector< vector<sheetNode> > csvtest ( getFile("testread.csv") );
  for(
    vector< vector<sheetNode> >::iterator it1 = csvtest.begin();
    it1 != csvtest.end();
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
  cout << "Testing Complete." << "\n";;
  return 0;
}
*/
