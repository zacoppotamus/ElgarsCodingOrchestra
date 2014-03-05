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

vector< vector<sheetNode> > readCSV( string filename )
{
  //Test code commented out
  //cout << "_" << "Filetype " << filetype << " detected." << "\n";
  ifstream input( filename );
  //cout << "_" << "File opened..." << "\n";
  if( !input.good() )
  {
    //cout << "_" << "File not opened successfully." << "\n";
    vector< vector<sheetNode> > failure;
    return failure;
  }
  //cout << "__" << "Initiating CSV reading tests..." << "\n";
  unsigned width = 0;
  unsigned height = 0;
  width = charsUntil( input, ',', '\n' ) + 1;
  height = charsUntil( input, '\n' );
  //cout << "__" << "Character counts obtained..." << "\n";
  vector<sheetNode> blankvector;
  vector< vector<sheetNode> > spreadsheet ( height, blankvector );
  //cout << "__" << "Vector generated..." << "\n";
  unsigned cHeight = 0;
  string csvalue;
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
      // cout << "File successfully read." << "\n";
      // cout << "Name: " << xmlFileName << "\n";
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
    // string initval = *it;
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
    // string endval = *it;
    // cout << initval << " -> " << endval << '\n';
  }
  return 1;
}

int fileIn( const char * filepath, unzFile zipFile )
{
  // cout << "Opening File..." << '\n';
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
      // cout << "File opened successfully!" << '\n';
      // cout << "File path: " << zipFileName.substr( 0, strlen( filepath ) )
      //   << '\n';
      // cout << "Comp path: " << filepath << '\n';
      int comparison = 
        zipFileName.substr( 0, strlen( filepath ) ).compare( filepath );
      if( comparison == 0 )
      {
        // cout << "Match!" << '\n';
        return 1;
      }
      else
      {
        // cout << "No Match." << '\n';
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
  coord upper = decodeDimension( topleft );
  coord lower = decodeDimension( bottomright );
  coord size;
  size.x = lower.x + 1 - upper.x;
  size.y = lower.y + 1 - upper.y;
  sheetNode blankcell;
  vector<sheetNode> blankvector( size.x, blankcell );
  spreadsheet.assign( size.y, blankvector );
  currentPosition = fileContents.find( "<c ", endPosition );
  string newDimension;
  coord newPosition;
  while( currentPosition != string::npos )
  {
    currentPosition += 6;
    endPosition = fileContents.find_first_of( '"', currentPosition );
    newDimension = fileContents.substr(
      currentPosition, endPosition - currentPosition );
    newPosition = decodeDimension( newDimension );
    newPosition.x -= upper.x;
    newPosition.y -= upper.y;
    currentPosition = fileContents.find( "t=", endPosition );
    currentPosition += 3;
    sheetNode * newCell;
    char cellType = fileContents[currentPosition];
    currentPosition = fileContents.find( "<v", currentPosition );
    currentPosition += 3;
    endPosition = fileContents.find_first_of( '<', currentPosition );
    string value = fileContents.substr(
      currentPosition, endPosition - currentPosition );
    stringstream converter( value );
    double newValue;
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
    spreadsheet[newPosition.y][newPosition.x] = *newCell;
    currentPosition = fileContents.find( "<c ", endPosition );
  }
  return 1;
}

int readXMLSheet( vector< vector< vector<sheetNode> > > &sheetList,
  const vector<string> &commonStrings, unzFile xlsxFile )
{
  // cout << "\t" << "Initialising XML reading protocols" << '\n';
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
      // cout << "\t" << "File successfully opened" << '\n';
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
      // cout << "\t" << "File Size = " << xmlFileSize << '\n';
      // cout << "\t" << "Max Buffer Size = " << maxBufferSize << '\n';
      if( xmlFileSize <= maxBufferSize )
      {
        // cout << "\t" << "Loading direct into buffer..." << '\n';
        xmlFileContents.resize( xmlFileSize );
        voidp xmlFileBuffer = &xmlFileContents[0];
        vector< vector<sheetNode> > spreadsheet;
        int fileReadStatus =
          unzReadCurrentFile( xlsxFile, xmlFileBuffer, xmlFileSize );
        if( fileReadStatus > 0 )
        {
          // cout << "\t" << "Reading contents of file..." << '\n';
          if( getSheetContents( spreadsheet,
            commonStrings, xmlFileContents ) == 1 )
          {
            // cout << "\t" << "File successfully read." << '\n';
            sheetList.push_back( spreadsheet );
            return 1;
          }
        }
      }
    }
  }
  return -1;
}

vector< vector< vector<sheetNode> > > readXLSX( string filename )
{
  vector< vector< vector<sheetNode> > > falseResult;
  // cout << "Beginning XLSX read tests..." << '\n';
  unzFile xlsxFile = unzOpen( filename.c_str() );
  if( xlsxFile )
  {
    int fileAccessStatus = unzGoToFirstFile( xlsxFile );
    int commonStringsFound = 0;
    vector<string> commonStrings;
    while( fileAccessStatus == UNZ_OK && commonStringsFound == 0 )
    {
      commonStringsFound = loadCommonStrings( commonStrings, xlsxFile );
      fileAccessStatus = unzGoToNextFile( xlsxFile );
    }
    if( commonStringsFound == 1 )
    {
      // cout << "Success! Printing " << commonStrings.size() <<
      //   " common strings..." << '\n';
      /*
      for( vector<string>::iterator it = commonStrings.begin();
        it != commonStrings.end();
        it++ )
      {
        cout << "\t" << *it << '\n';
      }
      */
    }
    else if( commonStringsFound == 0 )
    {
      // cout << "Common strings not found." << '\n';
      return falseResult;
    }
    else if( commonStringsFound == -1 )
    {
      // cout << "Invalid common strings file." << '\n';
      return falseResult;
    }
    if( !sanitizeXMLStrings( commonStrings ) )
    {
      // cout << "Invalid common string content." << '\n';
      return falseResult;
    }
    // cout << '\n';
    // cout << "Common strings sanitized." << '\n';
    /*
    for( vector<string>::iterator it = commonStrings.begin();
      it != commonStrings.end();
      it++ )
    {
      cout << "\t" << *it << '\n';
    }
    */
    vector< vector< vector<sheetNode> > > sheetList;
    fileAccessStatus = unzGoToFirstFile( xlsxFile );
    while( fileAccessStatus == UNZ_OK )
    {
      if( fileIn( "xl/worksheets/", xlsxFile ) )
      {
        // cout << "Spreadsheet detected, loading..." << '\n';
        readXMLSheet( sheetList, commonStrings, xlsxFile );
      }
      fileAccessStatus = unzGoToNextFile( xlsxFile );
    }
    // cout << '\n' << "File reading complete." << '\n';
    // cout << sheetList.size() << " spreadsheets loaded." << '\n';
    // cout << "Testing Complete." << "\n";
    return sheetList;
  }
  return falseResult;
}

vector< vector< vector<sheetNode> > > getFile( string fileName )
{
  // Test code commented out
  // cout << "_" << "Testing file access on " << fileName << "..." << "\n";
  FileType filetype = getType( fileName );
  if( filetype == CSV )
  {
    // cout << "_" << "Reading CSV file..." << "\n";
    vector< vector< vector<sheetNode> > > csvSheet( 1, readCSV( fileName ) ); 
    return csvSheet;
  }
  else if( filetype == XLSX )
  {
    // cout << "_" << "Reading XLSX file..." << "\n";
    return readXLSX( fileName );
  }
  else if( filetype == UNDEF )
  {
    // cout << "_" << "Undefined file type." << "\n";
    vector< vector< vector<sheetNode> > > failure;
    return failure;
  }
  vector< vector< vector<sheetNode> > > failure;
  return failure;
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
