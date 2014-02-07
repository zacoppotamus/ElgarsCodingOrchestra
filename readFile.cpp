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
  input.seekg( startPosition );
  return count;
}

string getCSV( ifstream &input )
{
  string result = "";
  char next = 0;
  bool quotes = false;
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
  }
  return result;
}

void insertValue( string csvalue, sheetNode * cell )
{
  string upper = simpleToUpper( csvalue );
  if( upper.compare( "TRUE" ) == 0 )
  {
    delete (cell);
    *cell = new sheetNode( true );
    return;
  }
  else if( upper.compare( "FALSE" ) == 0 )
  {
    delete (cell);
    *cell = new sheetNode( false );
    return;
  }
  else
  {
    stringstream conversion( csvalue );
    double numval;
    if( ( conversion >> numval ) )
    {
      delete (cell);
      *cell = new sheetNode( numval );
      return;
    }
    else
    {
      delete (cell);
      *cell = new sheetNode( csvalue );
      return;
    }
  }
}

sheetNode ** readCSV( ifstream &input )
{
  unsigned width = 0;
  unsigned height = 0;
  bool accept = false;
  width = charsUntil( input, ',', '\n' ) + 1;
  height = charsUntil( input, '\n' ) + 1;
  sheetNode ** spreadsheet = new sheetNode*[height];
  for( int count = 0; count < height; count++ )
  {
    spreadsheet[count] = new sheetNode[width]();
  }
  unsigned cHeight = 0;
  string csvalue;
  JType valueType;
  while( cHeight < height )
  {
    unsigned cWidth = 0;
    while( cWidth < width )
    {
      csvalue = getCSV( input );
      insertValue( csvalue, &spreadsheet[cHeight][cWidth] );
      cWidth++;
    }
    cHeight++;
  }
}

sheetNode ** getFile( string fileName )
{
  FileType filetype = getType( fileName );
  ifstream input( fileName );
  if( !input )
  {
    return NULL;
  }
  if( filetype == CSV )
  {
    return readCSV( input );
  }
  else if( filetype == UNDEF )
  {
    return NULL;
  }
}

int main()
{
  sheetNode ** test = getFile("test.csv");
  return 0;
}
