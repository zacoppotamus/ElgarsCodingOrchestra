#include <iostream>
#include <fstream>
#include <string>
#include <sstream>
#include <vector>
#include <cstring>
#include "readFile.h"
#include "unzip.h"

#define MAX_STRING_SIZE 256

const char* SHARED_STRINGS_PATH = "xl/sharedStrings.xml";

typedef struct
{
  unsigned x;
  unsigned y;
} coord;

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

sheetNode::sheetNode( long double newNumber )
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

long double sheetNode::getNumber()
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
  for( unsigned count = 0; count<length; count++ )
    if( upper[count] > 96 && upper[count] < 123 )
      upper[count] -= 32;
  return upper;
}

FileType getType( string fileName )
{
  unsigned marker = fileName.find_last_of('.');
  string fileStr = fileName.substr(marker+1);
  fileStr = simpleToUpper( fileStr );
  if( fileStr.compare( "CSV" ) == 0 )
    return CSV;
  else if( fileStr.compare( "XLSX" ) == 0 )
    return XLSX;
  else if( fileStr.compare( "ODS" ) == 0 )
    return ODS;
  else
    return UNDEF;
}

string pageString( page spreadsheet )
{
  string output = "";
  output += spreadsheet.name + '\n';
  for( 
    vector< vector<sheetNode> >::iterator outIt = spreadsheet.contents.begin();
    outIt != spreadsheet.contents.end();
    outIt++
  )
  {
    for( vector<sheetNode>::iterator inIt = outIt->begin();
      inIt != outIt->end();
      inIt++
    )
    {
      JType type = inIt->getType();
      switch( type ){
        case STRING:
          output += 'S';
          break;
        case NUMBER:
          output += 'N';
          break;
        case BOOL:
          output += 'B';
          break;
        default:
          output += '_';
          break;
      }
      output = output + '\t';
    }
    output = output + '\n';
  }
  return output;
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
  string result = "";
  char next = 0;
  bool quotes = false;
  if( !input.good() )
  {
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
  }
  return result;
}

string purgeQuotes( string value )
{
  if( value.size() > 2 )
  {
    if( value[0] == '"' && value[value.size()-1] == '"' )
    {
      string newvalue = value.substr( 1, value.size()-2 );
      return newvalue;
    }
  }
  return value;
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
  if( upper[upper.size()-1] == 13 )
  {
    upper.resize( upper.size()-1 );
  }
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
    long double numval;
    if( ( conversion >> numval ) )
    {
      sheetNode newsheet( numval );
      cell.push_back( newsheet );
      return;
    }
    else
    {
      string fixedValue = purgeQuotes( csvalue );
      sheetNode newsheet( fixedValue );
      cell.push_back( newsheet );
      return;
    }
  }
}

page readCSV( string filename )
{
  ifstream input( filename );
  string title = filename;
  title.erase( title.find_last_of( '.' ) );
  if( !input.good() )
  {
    page failure;
    failure.name = title;
    return failure;
  }
  unsigned width = 0;
  unsigned height = 0;
  width = charsUntil( input, ',', '\n' ) + 1;
  height = charsUntil( input, '\n' );
  vector<sheetNode> blankvector;
  vector< vector<sheetNode> > blanksheet ( height, blankvector );
  page spreadsheet;
  spreadsheet.name = title;
  spreadsheet.contents = blanksheet;
  unsigned cHeight = 0;
  string csvalue;
  while( cHeight < height )
  {
    unsigned cWidth = 0;
    while( cWidth < width )
    {
      csvalue = getCSV( input );
      insertValue( csvalue, spreadsheet.contents[cHeight] );
      cWidth++;
    }
    cHeight++;
  }
  return spreadsheet;
}

//Returns -1 if string is invalid
coord decodeDimension( string dimension )
{
  coord failure;
  failure.x = -1;
  failure.y = -1;
  int width = 0;
  int height = 0;
  size_t lcount = 0;
  while( dimension[lcount] >= 'A' && dimension[lcount] <= 'Z' )
  {
    if( lcount == dimension.size() )
    {
      return failure;
    }
    lcount++;
  }
  string letters = dimension.substr( 0, lcount );
  int value;
  for( size_t count = 0; count < lcount; count++ )
  {
    width *= 26;
    value = letters[count] + 1 - 'A';
    if( value < 1 || value > 26 )
    {
      return failure;
    }
    width += value;
  }
  string numbers = dimension.substr( lcount );
  stringstream converter( numbers );
  if( !( converter >> height ) )
  {
    return failure;
  }
  coord result;
  result.x = width;
  result.y = height;
  return result;
}

int getCommonStrings( vector<string> &commonStrings,
  const string &fileContents )
{
  string startTag("<t>");
  string endTag("</t>");
  size_t currentPos = fileContents.find( startTag );
  size_t nextPos;
  size_t len;
  while( currentPos != string::npos )
  {
    currentPos += 3;
    nextPos = fileContents.find( endTag, currentPos );
    if( nextPos == string::npos )
    {
      return -1;
    }
    len = nextPos - currentPos;
    string newString( fileContents.substr( currentPos, len ) );
    commonStrings.push_back( newString );
    currentPos = fileContents.find( startTag, currentPos );
  }
  return 1;
}

int loadCommonStrings( vector<string> &commonStrings,
  unzFile xlsxFile )
{
  if( unzOpenCurrentFile( xlsxFile ) == UNZ_OK )
  {
    unz_file_info xmlFileInfo; 
    memset( &xmlFileInfo, 0, sizeof( unz_file_info ) );
    if( unzGetCurrentFileInfo( xlsxFile,
      &xmlFileInfo,
      NULL, 0,
      NULL, 0,
      NULL, 0 ) == UNZ_OK )
    {
      string xmlFileName;
      xmlFileName.resize( xmlFileInfo.size_filename );
      unzGetCurrentFileInfo( xlsxFile,
        &xmlFileInfo,
        &xmlFileName[0], xmlFileInfo.size_filename,
        NULL, 0,
        NULL, 0 );
      if( xmlFileName.compare( SHARED_STRINGS_PATH ) == 0 )
      {
        string xmlFileContents;
        uLong xmlFileSize = xmlFileInfo.uncompressed_size;
        unsigned long long maxBufferSize = 1;
        if( sizeof( unsigned long long ) == sizeof( unsigned ) )
        {
          maxBufferSize = -1;
        }
        else
        {
          maxBufferSize <<= sizeof( unsigned ) * 8;
          maxBufferSize--;
        }
        if( xmlFileSize <= maxBufferSize )
        {
          xmlFileContents.resize( xmlFileSize );
          voidp xmlFileBuffer = &xmlFileContents[0];
          if( unzReadCurrentFile( xlsxFile, xmlFileBuffer, xmlFileSize ) )
          {
            if( getCommonStrings( commonStrings, xmlFileContents ) == 1 )
            {
              return 1;
            }
          }
        }
        return -1;
      }
    }
  }
  return 0;
}

int sanitizeXMLStrings( vector<string> &strings )
{
  size_t currentPos;
  size_t endPos;
  size_t len;
  for( vector<string>::iterator it = strings.begin();
    it != strings.end();
    it++ )
  {
    currentPos = it->find_first_of( '&' );
    while( currentPos != string::npos )
    {
      endPos = it->find_first_of( ';', currentPos );
      if( endPos == string::npos )
      {
        return 0;
      }
      len = endPos - currentPos + 1;
      string token = it->substr( currentPos, len );
      if( len == 6 )
      {
        if( token.compare( "&quot;" ) == 0 )
        {
          it->replace( currentPos, 6, """" );
        }
        else if( token.compare( "&apos;" ) == 0 )
        {
          it->replace( currentPos, 6, "'" );
        }
        else
        {
          return 0;
        }
      }
      else if( len == 5 )
      {
        if( token.compare( "&amp;" ) == 0 )
        {
          it->replace( currentPos, 5, "&" );
        }
        else
        {
          return 0;
        }
      }
      else if( len == 4 )
      {
        if( token.compare( "&lt;" ) == 0 )
        {
          it->replace( currentPos, 4, "<" );
        }
        else if( token.compare( "&gt;" ) == 0 )
        {
          it->replace( currentPos, 4, ">" );
        }
        else
        {
          return 0;
        }
      }
      else
      {
        return 0;
      }
      currentPos = it->find_first_of( '&', currentPos );
    }
  }
  return 1;
}

int fileIn( const char * filepath, unzFile zipFile )
{
  if( unzOpenCurrentFile( zipFile ) == UNZ_OK )
  {
    unz_file_info zipFileInfo;
    memset( &zipFileInfo, 0, sizeof( unz_file_info ) );
    if( unzGetCurrentFileInfo( zipFile,
      &zipFileInfo,
      NULL, 0,
      NULL, 0,
      NULL, 0 ) == UNZ_OK )
    {
      string zipFileName;
      zipFileName.resize( zipFileInfo.size_filename );
      unzGetCurrentFileInfo( zipFile,
        &zipFileInfo,
        &zipFileName[0], zipFileInfo.size_filename,
        NULL, 0,
        NULL, 0 );
      unzCloseCurrentFile( zipFile );
      int comparison = 
        zipFileName.substr( 0, strlen( filepath ) ).compare( filepath );
      if( comparison == 0 )
      {
        return 1;
      }
      else
      {
        return 0;
      }
    }
  }
  return -1;
}

int fileIn( const char * filepath, unzFile zipFile, string &title )
{
  if( unzOpenCurrentFile( zipFile ) == UNZ_OK )
  {
    unz_file_info zipFileInfo;
    memset( &zipFileInfo, 0, sizeof( unz_file_info ) );
    if( unzGetCurrentFileInfo( zipFile,
      &zipFileInfo,
      NULL, 0,
      NULL, 0,
      NULL, 0 ) == UNZ_OK )
    {
      string zipFileName;
      zipFileName.resize( zipFileInfo.size_filename );
      unzGetCurrentFileInfo( zipFile,
        &zipFileInfo,
        &zipFileName[0], zipFileInfo.size_filename,
        NULL, 0,
        NULL, 0 );
      unzCloseCurrentFile( zipFile );
      int comparison = 
        zipFileName.substr( 0, strlen( filepath ) ).compare( filepath );
      if( comparison == 0 )
      {
        title = zipFileName;
        title = title.substr( title.find_last_of( '/' ) + 1 );
        title.erase( title.find_last_of( '.' ) );
        return 1;
      }
      else
      {
        return 0;
      }
    }
  }
  return -1;
}

int getSheetContents( vector< vector<sheetNode> > &spreadsheet,
  const vector<string> &commonStrings, const string &fileContents )
{
  size_t currentPosition;
  size_t endPosition;
  currentPosition = fileContents.find( "<d" );
  currentPosition += 16;
  endPosition = fileContents.find_first_of( ':', currentPosition );
  string topleft = fileContents.substr(
    currentPosition, endPosition - currentPosition );
  currentPosition = endPosition + 1;
  endPosition = fileContents.find_first_of( '"', currentPosition );
  string bottomright = fileContents.substr(
    currentPosition, endPosition - currentPosition );
  coord lower = decodeDimension( bottomright );
  coord upper = decodeDimension( topleft );
  coord size;
  size.x = lower.x + 1 - upper.x;
  size.y = lower.y + 1 - upper.y;
  if( (int)lower.x == -1 || (int)upper.x == -1 || (int)lower.y == -1 ||
    (int)upper.y == -1 || (int)size.x == 0 || (int)size.y == 0 )
  {
    return 0;
  }
  sheetNode blankcell;
  vector<sheetNode> blankvector( size.x, blankcell );
  spreadsheet.assign( size.y, blankvector );
  currentPosition = fileContents.find( "<c ", endPosition );
  string newDimension;
  coord newPosition;
  size_t nextType;
  size_t nextCell;
  while( currentPosition != string::npos )
  {
    currentPosition += 6;
    endPosition = fileContents.find_first_of( '"', currentPosition );
    newDimension = fileContents.substr(
      currentPosition, endPosition - currentPosition );
    newPosition = decodeDimension( newDimension );
    newPosition.x -= upper.x;
    newPosition.y -= upper.y;
    nextType = fileContents.find( " t=", endPosition );
    nextCell = fileContents.find_first_of( '/', endPosition );
    sheetNode * newCell;
    if( nextType > nextCell )
    {
      newCell = new sheetNode();
    }
    else
    {
      currentPosition = nextType + 4;
      char cellType = fileContents[currentPosition];
      currentPosition = fileContents.find( "<v", currentPosition );
      currentPosition += 3;
      endPosition = fileContents.find_first_of( '<', currentPosition );
      string value = fileContents.substr(
        currentPosition, endPosition - currentPosition );
      stringstream converter( value );
      long double newValue;
      if( !( converter >> newValue ) )
      {
        newValue = 0;
      }
      switch( cellType )
      {
        case 's':
          newCell = new sheetNode( commonStrings[(int)newValue] );
          break;
        case 'n':
          newCell = new sheetNode( newValue );
          break;
        case 'b':
          if( newValue == 0 )
          {
            newCell = new sheetNode( false );
          }
          else
          {
            newCell = new sheetNode( true );
          }
          break;
        default:
          newCell = new sheetNode();
          break;
      }
    }
    spreadsheet[newPosition.y][newPosition.x] = *newCell;
    currentPosition = fileContents.find( "<c ", endPosition );
  }
  return 1;
}

int readXMLSheet( vector< page > &sheetList, string title,
  const vector<string> &commonStrings, unzFile xlsxFile )
{
  if( unzOpenCurrentFile( xlsxFile ) == UNZ_OK )
  {
    unz_file_info xmlFileInfo;
    memset( &xmlFileInfo, 0, sizeof( unz_file_info ) );
    if( unzGetCurrentFileInfo( xlsxFile,
      &xmlFileInfo,
      NULL, 0,
      NULL, 0,
      NULL, 0 ) == UNZ_OK )
    {
      string xmlFileContents;
      uLong xmlFileSize = xmlFileInfo.uncompressed_size;
      unsigned long long maxBufferSize = 1;
      if( sizeof( unsigned long long ) == sizeof( unsigned ) )
      {
        maxBufferSize = -1;
      }
      else
      {
        maxBufferSize <<= sizeof( unsigned ) * 8;
        maxBufferSize--;
      }
      if( xmlFileSize <= maxBufferSize )
      {
        xmlFileContents.resize( xmlFileSize );
        voidp xmlFileBuffer = &xmlFileContents[0];
        vector< vector<sheetNode> > spreadsheet;
        int fileReadStatus =
          unzReadCurrentFile( xlsxFile, xmlFileBuffer, xmlFileSize );
        if( fileReadStatus > 0 )
        {
          if( getSheetContents( spreadsheet,
            commonStrings, xmlFileContents ) == 1 )
          {
            page newsheet;
            newsheet.name = title;
            newsheet.contents = spreadsheet;
            sheetList.push_back( newsheet );
            return 1;
          }
        }
      }
    }
  }
  return -1;
}

//Necessarily takes an already opened unzFile and leaves it open
int getCommonStrings( vector<string> &commonStrings, unzFile xlsxFile )
{
  int commonStringsFound = 0;
  int fileAccessStatus = unzGoToFirstFile( xlsxFile );
  while( fileAccessStatus == UNZ_OK && commonStringsFound == 0 )
  {
    commonStringsFound = loadCommonStrings( commonStrings, xlsxFile );
    fileAccessStatus = unzGoToNextFile( xlsxFile );
  }
  if( commonStringsFound != 1 )
  {
    return commonStringsFound;
  }
  if( !sanitizeXMLStrings( commonStrings ) )
  {
    return -1;
  }
  return 1;
}

int getXMLSheets( string filename, vector< page > &sheetList,
  vector<string> commonStrings, unzFile xlsxFile )
{
  int fileAccessStatus = unzGoToFirstFile( xlsxFile );
  while( fileAccessStatus == UNZ_OK )
  {
    string title;
    if( fileIn( "xl/worksheets/", xlsxFile, title ) )
    {
      string pageName = filename;
      pageName.resize( pageName.find_last_of('.') );
      pageName = pageName + "_" + title;
      readXMLSheet( sheetList, pageName, commonStrings, xlsxFile );
    }
    fileAccessStatus = unzGoToNextFile( xlsxFile );
  }
  return 1;
}

vector< page > readXLSX( string filename )
{
  vector< page > falseResult;
  unzFile xlsxFile = unzOpen( filename.c_str() );
  if( xlsxFile )
  {
    vector<string> commonStrings;
    int commonStringsFound = getCommonStrings( commonStrings, xlsxFile );
    if( commonStringsFound == 0 )
    {
      return falseResult;
    }
    else if( commonStringsFound == -1 )
    {
      return falseResult;
    }
    vector< page > sheetList;
    int dataFound = getXMLSheets(filename, sheetList, commonStrings, xlsxFile);
    if( dataFound != 1 )
    {
      return falseResult;
    }
    return sheetList;
  }
  return falseResult;
}

vector< page > getFile( string fileName )
{
  FileType filetype = getType( fileName );
  if( filetype == CSV )
  {
    vector< page > csvSheet( 1, readCSV( fileName ) ); 
    return csvSheet;
  }
  else if( filetype == XLSX )
  {
    return readXLSX( fileName );
  }
  vector< page > failure;
  return failure;
}

