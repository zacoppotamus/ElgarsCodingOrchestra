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

// Section ends with cc = " and ifs.peek() != " regardless of the
// length/meaning of the quote.
void skipQuotes( long unsigned &pos, ifstream &ifs, unsigned &error )
{
  char cc;
  cc = ifs.get(); pos++;
  if( cc != '\"' )
  {
    while( ifs.good() && cc != '\"' )
    {
      cc = ifs.get(); pos++;
      if( cc == '\"' && ifs.peek() == '\"' )
      {
        ifs.get(); pos++; cc = ifs.get(); pos++;
      }
    }
    if( ifs.eof() )
    {
      error = 2;
      return;
    }
    if( ifs.fail() )
    {
      error = 1;
      return;
    }
  }
}

void writeCell( sheet &target, size_t row, size_t col, string record )
{
  if( record == "" ) return;
  long double number;
  if( convertData( record, number ) )
  {
    target[row][col].setValue( number );
    return;
  }
  bool boolean;
  if( convertBool( record, boolean ) )
  {
    target[row][col].setValue( boolean );
    return;
  }
  target[row][col].setValue( record );
}

void csvMarkers( vector<long unsigned> &commas, vector<long unsigned> &newls,
    ifstream &ifs, unsigned &error )
{
  long unsigned pos = 0;
  char cc = ifs.get();
  while( ifs.good() )
  {
    if( cc == '\"' ) skipQuotes( pos, ifs, error );
    if( error != 0 ) return;
    if( cc == ',' ) commas.push_back( pos );
    if( cc == '\n' ) newls.push_back( pos );
    cc = ifs.get();
    pos++;
  }
  if( ifs.bad() ) error = 1;
}

void csvDimensions( size_t &rows, size_t &cols,
    const vector<long unsigned> &commas, const vector<long unsigned> &newls )
{
  size_t max = newls.size();
  size_t commaPos = 0;
  size_t newlPos = 0;
  long unsigned nextComma = ( commas.size() > 0 ) ? commas[0] : -1;
  long unsigned nextNewl = ( newls.size() > 0 ) ? newls[0] : -1;
  size_t dimensionPos = 0;
  vector<size_t> dimensions(1,1);
  while( newlPos < max )
  {
    nextNewl = ( newls.size() > newlPos ) ? newls[newlPos] : -1;
    while( nextComma < nextNewl )
    {
      dimensions[dimensionPos]++;
      commaPos++;
      nextComma = ( commas.size() > commaPos ) ? commas[commaPos] : -1;
    }
    dimensions.push_back(1);
    dimensionPos++;
    newlPos++;
  }
  rows = dimensions.size();
  cols = 0;
  for( vector<size_t>::iterator it = dimensions.begin();
      it != dimensions.end(); it++ )
  {
    if( *it > cols ) cols = *it;
  }
}

void csvData( vector<long unsigned> &commas, vector<long unsigned> &newls,
    sheet &result, size_t rows, size_t cols, ifstream &ifs, unsigned &error )
{
  long unsigned pos = 0;
  size_t commaPos = 0;
  size_t newlPos = 0;
  long unsigned nextComma = ( commas.size() > 0 ) ? commas[0] : -1;
  long unsigned nextNewl = ( newls.size() > 0 ) ? newls[0] : -1;
  size_t row = 0;
  size_t col = 0;
  while( ifs.good() )
  {
    col = 0;
    while( nextComma < nextNewl )
    {
      string record;
      while( pos < nextComma )
      {
        char cc = ifs.get(); pos++;
        if( cc == '\"' )
        {
          cc = ifs.get(); pos++;
        }
        record += cc;
      }
      writeCell( result, row, col, record );
      ifs.get(); pos++;
      col++;
      commaPos++;
      nextComma = ( commas.size() > commaPos ) ? commas[commaPos] : -1;
    }
    string record;
    while( pos < nextNewl && !ifs.eof() )
    {
      char cc = ifs.get(); pos++;
      if( cc == '\"' )
      {
        cc = ifs.get(); pos++;
      }
      if( ifs.good() ) record += cc;
    }
    writeCell( result, row, col, record );
    ifs.get(); pos++;
    while( col+1 < cols )
    {
      col++;
      writeCell( result, row, col, "" );
    }
    row++;
    newlPos++;
    nextNewl = ( newls.size() > newlPos ) ? newls[newlPos] : -1;
  }
  if( ifs.bad() ) error = 1;
}

sheet readCSV( string filename, unsigned &error )
{
  error = 0;
  ifstream ifs( filename );
  sheet fail( 0, 0 );
  vector<long unsigned> commas;
  vector<long unsigned> newls;
  cout << "marka" << '\n';
  csvMarkers( commas, newls, ifs, error );
  if( error != 0 ) return fail;
  size_t rows;
  size_t cols;
  cout << "dim" << '\n';
  csvDimensions2( rows, cols, commas, newls );
  sheet result( rows, cols );
  ifs.close();
  ifs.open( filename );
  cout << "dat" << '\n';
  csvData2( commas, newls, result, rows, cols, ifs, error );
  cout << "dun" << '\n';
  if( error != 0 ) return fail;
  return result;
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
  unsigned error;
  FileType filetype = getType( fileName );
  if( filetype == CSV )
  {
    vector< page > csvSheet( 1, readCSV( fileName, error ) ); 
    return csvSheet;
  }
  else if( filetype == XLSX )
  {
    return readXLSX( fileName );
  }
  vector< page > failure;
  return failure;
}

