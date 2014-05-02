#include <iostream>
#include <functional>
#include <fstream>
#include <string>
#include <sstream>
#include <vector>
#include <cstring>
#include "unzip.h"
#include "sadUtils.hpp"
#include "sadReader.hpp"

using namespace std;

const char* SHARED_STRINGS_PATH = "xl/sharedStrings.xml";

////////////////////////////////////////////////////////////////////////////////
////////  CELL MEMBER FUNCTIONS
////////////////////////////////////////////////////////////////////////////////

cell::cell()
{
  jType= NULLVALUE;
}

cell::cell( std::string newString )
{
  setValue( newString );
}

cell::cell( long double newNumber )
{
  setValue( newNumber );
}

cell::cell( bool newBool )
{
  setValue( newBool );
}

void cell::setValue( std::string input )
{
  jType = STRING;
  strval = input;
}

void cell::setValue( long double input )
{
  jType = NUMBER;
  numval = input;
}

void cell::setValue( bool input )
{
  jType = BOOL;
  boolval = input;
}

JType cell::getType()
{
  return jType;
}

string cell::getString()
{
  if( jType == STRING ) return strval;
  return "";
}

long double cell::getNumber()
{
  if( jType == NUMBER ) return numval;
  return 0;
}

bool cell::getBool()
{
  if( jType == BOOL ) return boolval;
  return false;
}

////////////////////////////////////////////////////////////////////////////////
////////  XMLNODE MEMBER FUNCTIONS
////////////////////////////////////////////////////////////////////////////////

xmlNode::xmlNode( string input )
{
  name = input;
  attC = 0;
  childC = 0;
  contentC = 0;
}

void xmlNode::addAttribute( string attribute, string value )
{
  attC++;
  attributes.push_back( attribute );
  values.push_back( value );
}

void xmlNode::addChild( xmlNode* child )
{
  childC++;
  children.push_back( child );
}

void xmlNode::addContent( string input )
{
  contentC++;
  content.push_back( input );
}

string xmlNode::getName()
{
  return name;
}

vector<string> xmlNode::getAttribute( size_t index )
{
  vector<string> attribute;
  if( index >= 0 && index < attC )
  {
    attribute.push_back( attributes[index] );
    attribute.push_back( values[index] );
  }
  return attribute;
}

size_t xmlNode::attributeCount()
{
  return attC;
}

xmlNode* xmlNode::getChild( size_t index )
{
  if( index >= 0 && index < childC ) return children[index];
  return 0;
}

size_t xmlNode::childCount()
{
  return childC;
}

std::string xmlNode::getContent( size_t index )
{
  if( index >= 0 && index < contentC ) return content[index];
  return "";
}

size_t xmlNode::contentCount()
{
  return contentC;
}

////////////////////////////////////////////////////////////////////////////////
////////  SHEET MEMBER FUNCTIONS
////////////////////////////////////////////////////////////////////////////////

sheet::sheet( size_t rows, size_t columns, string name ) : rowc(rows),
  colc(columns)
{
  sName = name;
  cell blank;
  vector<cell> column( colc, blank );
  contents.insert( contents.end(), rowc, column );
}

size_t sheet::rows()
{
  return rowc;
}

size_t sheet::cols()
{
  return colc;
}

string sheet::name()
{
  return sName;
}

////////////////////////////////////////////////////////////////////////////////
////////  CSV FILE READING
////////////////////////////////////////////////////////////////////////////////

// Section ends with cc = " and ifs.peek() != " regardless of the
// length/meaning of the quote.
void skipQuotes( long unsigned &pos, istream &ifs, unsigned &error )
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
    istream &ifs, unsigned &error )
{
  long unsigned pos = 0;
  char cc = ifs.get();
  while( ifs.good() )
  {
    if( cc == '\"' ) skipQuotes( pos, ifs, error );
    if( error != 0 ) return;
    if( cc == ',' ) commas.push_back( pos );
    if( cc == '\n' || cc == '\r' ) newls.push_back( pos );
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
    sheet &result, size_t rows, size_t cols, istream &ifs, unsigned &error )
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
  csvMarkers( commas, newls, ifs, error );
  if( error != 0 ) return fail;
  size_t rows;
  size_t cols;
  csvDimensions( rows, cols, commas, newls );
  string name = filename.substr( 0, filename.find_last_of('.') );
  sheet result( rows, cols, name );
  ifs.close();
  ifs.open( filename );
  csvData( commas, newls, result, rows, cols, ifs, error );
  if( error != 0 ) return fail;
  return result;
}

////////////////////////////////////////////////////////////////////////////////
////////  XML FILE READING
////////////////////////////////////////////////////////////////////////////////

// WARNING: Currently there is no support for non-ascii (i.e. full unicode )
// characters, beware!

// Returns true if c is a valid start to an xml name
bool xmlNameStart( char c )
{
  if( (c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z') || c == ':' || c == '_' )
  {
    return true;
  }
  return false;
}

bool xmlName( char c )
{
  if( xmlNameStart(c) || c == '-' || c == '.' || (c >= '0' && c <= '9') )
  {
    return true;
  }
  return false;
}

// Progresses the stream one xml text character forward, while skipping over
// literal escape strings
// Returns the xml text character
char xmlChar( istream &ifs, unsigned &error )
{
  error = 0;
  char cc;
  try
  {
    cc = ifs.get();
    if( cc == '&' )
    {
      string text;
      cc = ifs.get();
      while( cc != ';' )
      {
        text += cc;
        if( text.size() > 4 )
        {
          error = 2;
          return cc;
        }
        cc = ifs.get();
      }
      if( text == "lt" ) return '<';
      else if( text == "gt" ) return '>';
      else if( text == "amp" ) return '&';
      else if( text == "apos" ) return '\'';
      else if( text == "quot" ) return '\"';
      else
      {
        error = 2;
        return cc;
      }
    }
    return cc;
  }
  catch( istream::failure e )
  {
    if( ifs.fail() ) error = 1;
    else error = 3;
    ifs.clear();
  }
  return cc;
}

// Takes a stream whose next character is the first character of the content
// section of a tag, and sets the value of "text" to the content.
// Note: Takes the stream to the start of the next tag (last character is '<')
void xmlContent( string &text, istream &ifs, unsigned &error )
{
  //cout << "content" << '\n';
  char cc;
  string input;
  while( ifs.peek() != '<' )
  {
    cc = xmlChar( ifs, error );
    if( error != 0 ) return;
    input += cc;
  }
  text = input;
  ifs.get();
}

// Takes a stream whose next character is the first character of an attribute
// name, and sets the value of "name" and "value" to the attribute values.
// Note: Takes stream to the end of any following whitespace.
// Extra note: Exception mask of stream should be set to catch all errors
void xmlAttribute( string &name, string &value, istream &ifs,
    unsigned &error )
{
  //cout << "attribute" << '\n';
  char delim;
  char cc;
  value = "";
  cc = ifs.get();
  while( xmlName(cc) )
  {
    name += cc;
    cc = ifs.get();
  }
  while( whitespace(cc) ) cc = ifs.get();
  if( cc != '=' )
  {
    error = 2;
    return;
  }
  cc = ifs.get();
  while( whitespace(cc) ) cc = ifs.get();
  if( cc != '"' && cc != '\'' )
  {
    error = 2;
    return;
  }
  delim = cc;
  cc = ifs.get();
  while( cc != delim )
  {
    value += cc;
    cc = ifs.get();
  }
  while( whitespace(ifs.peek()) ) cc = ifs.get();
  error = 0;
}

void xmlTagName( string &name, istream &ifs, unsigned &error )
{
  //cout << "tagname" << '\n';
  string input;
  input += ifs.get();
  while( xmlName(ifs.peek()) ) input += ifs.get();
  name = input;
  while( whitespace(ifs.peek()) ) ifs.get();
  if( !ifs.good() )
  {
    if( ifs.eof() ) error = 3;
    else error = 1;
  }
}

// Assumed position of stream at time of calling:
// <tag-name abc...
//           ^
// (i.e. the first ifs.get() would return 'a')
// 
// Guaranteed position at end of call:
// xyz> abc...
//      ^
// (i.e. the next ifs.get() would return 'a')
//
// This is a fairly basic parser that only handles tags, content, and
// attributes. Fortunately, that's all that -should- be required. In the event
// that unaccounted-for input is encountered, the error code will be set
// accordingly.
// Error codes:
// 0: Success
// 1: File I/O error
// 2: Unparsable file
// 3: EOF reached early

void xmlTag( xmlNode *result, istream &ifs, unsigned &error )
{
  //cout << "tag" << '\n';
  error = 0;
  char cc;
  // Read tag internals 
  while( xmlNameStart(ifs.peek()) )
  {
    string name;
    string value;
    xmlAttribute( name, value, ifs, error );
    result->addAttribute( name, value );
    if( error != 0 ) return;
  }
  cc = ifs.get();
  if( cc != '/' && cc != '>' )
  {
    error = 2;
    return;
  }
  if( cc == '/' )
  {
    cc = ifs.get();
    //cout << "empty: " << cc << "->" << (char)ifs.peek() << '\n';
    return;
  }
  string text;
  // Obtain content, including contained tags
  while( true )
  {
    xmlContent( text, ifs, error );
    result->addContent( text );
    if( error != 0 ) return;
    if( ifs.peek() == '/' )
    {
      cc = ifs.get();
      while( cc != '>' ) cc = ifs.get();
      while( whitespace(ifs.peek()) ) ifs.get();
      return;
    }
    xmlTagName( text, ifs, error );
    if( error != 0 ) return;
    xmlNode* child = new xmlNode( text );
    xmlTag( child, ifs, error );
    if( error != 0 ) return;
    result->addChild( child );
  }
}

void xmlMetaTag( xmlNode *result, istream &ifs, unsigned &error )
{
  //cout << "metatag" << '\n';
  try
  {
    error = 0;
    char cc;
    // Read tag internals 
    while( xmlNameStart(ifs.peek()) )
    {
      string name;
      string value;
      xmlAttribute( name, value, ifs, error );
      result->addAttribute( name, value );
      if( error != 0 ) return;
    }
    cc = ifs.get();
    if( cc != '?' || ifs.peek() != '>' )
    {
      error = 2;
      return;
    }
    ifs.get();
    while( whitespace(ifs.peek()) ) ifs.get();
  }
  catch( istream::failure e )
  {
    if( ifs.fail() ) error = 1;
    else error = 3;
    ifs.clear();
  }
}

void xmlDocument( xmlNode *result, string &content, unsigned &error )
{
  //cout << "document" << '\n';
  char cc;
  istringstream ifs( content );
  while( !ifs.eof() && whitespace(ifs.peek())  ) ifs.get();
  while( !ifs.eof() )
  {
    cc = ifs.get();
    if( cc != '<' )
    {
      error = 2;
      return;
    }
    string name;
    xmlNode* child;
    if( ifs.peek() == '?' )
    {
      ifs.get();
      xmlTagName( name, ifs, error );
      if( error != 0 ) return;
      child = new xmlNode( name );
      xmlMetaTag( child, ifs, error ); 
      if( error != 0 ) return;
    }
    else
    {
      xmlTagName( name, ifs, error );
      if( error != 0 ) return;
      child = new xmlNode( name );
      xmlTag( child, ifs, error ); 
      if( error != 0 ) return;
    }
    result->addChild( child );
    while( !ifs.eof() && whitespace(ifs.peek())  ) ifs.get();
  }
  error = 0;
}

void printXMLTree( xmlNode* root, ostream &out, unsigned ind = 0 )
{
  out << tab(ind) << root->getName() << ":" << '\n';
  ind++;
  for( size_t it = 0; it < root->attributeCount(); it++ )
  {
    vector<string> attribute = root->getAttribute(it);
    out << tab(ind) << attribute[0] << "=""" << attribute[1] << """" << '\n';
  }
  string content = root->getContent(0);
  if( content != "" ) out << tab(ind) << content << '\n';
  for( size_t it = 0; it < root->childCount(); it++ )
  {
    xmlNode* child = root->getChild(it);
    printXMLTree( child, out, ind );
    // Due to the way nodes are formatted, there should always be n children and
    // n+1 contents
    content = root->getContent(it+1);
    if( content != "" ) out << tab(ind) << content << '\n';
  }
}

// Returns true if the current file of "zipFile" begins with "title". This is
// used both for identifying files in a directory as well as exact files.
// Sets title to the full file name and path
bool fileMatch( unzFile uzf, string &title, unsigned &error )
{
  if( unzOpenCurrentFile( uzf ) == UNZ_OK )
  {
    unz_file_info uzfInfo;
    memset( &uzfInfo, 0, sizeof( unz_file_info ) );
    if( unzGetCurrentFileInfo( uzf,
      &uzfInfo,
      NULL, 0,
      NULL, 0,
      NULL, 0 ) == UNZ_OK )
    {
      string uzfName;
      uzfName.resize( uzfInfo.size_filename );
      unzGetCurrentFileInfo( uzf,
        &uzfInfo,
        &uzfName[0], uzfInfo.size_filename,
        NULL, 0,
        NULL, 0 );
      unzCloseCurrentFile( uzf );
      string uzfNameCut = uzfName.substr( 0, title.size() );
      if( uzfNameCut == title )
      {
        title = uzfName;
        return true;
      }
      return false;
    }
  }
  error = 1;
  return false;
}

//Returns a list of the filenames of every file in the archive
vector<string> getArchiveFiles( unzFile uzf, unsigned &error )
{
  vector<string> result;
  int fileAccessStatus = unzGoToFirstFile( uzf );
  while( fileAccessStatus == UNZ_OK )
  {
    if( unzOpenCurrentFile( uzf ) == UNZ_OK )
    {
      unz_file_info uzfInfo;
      memset( &uzfInfo, 0, sizeof( unz_file_info ) );
      if( unzGetCurrentFileInfo( uzf,
        &uzfInfo,
        NULL, 0,
        NULL, 0,
        NULL, 0 ) == UNZ_OK )
      {
        string uzfName;
        uzfName.resize( uzfInfo.size_filename );
        unzGetCurrentFileInfo( uzf,
          &uzfInfo,
          &uzfName[0], uzfInfo.size_filename,
          NULL, 0,
          NULL, 0 );
        unzCloseCurrentFile( uzf );
        result.push_back( uzfName );
      }
    }
    else
    {
      error = 1;
      return result;
    }
    fileAccessStatus = unzGoToNextFile( uzf );
  }
  return result;
}

string loadArchiveFile( unzFile uzf, string filename, unsigned &error )
{
  int fileAccessStatus = unzGoToFirstFile( uzf );
  while( fileAccessStatus == UNZ_OK )
  {
    if( fileMatch( uzf, filename, error ) )
    {
      if( unzOpenCurrentFile( uzf ) == UNZ_OK )
      {
        unz_file_info archiveFileInfo; 
        memset( &archiveFileInfo, 0, sizeof( unz_file_info ) );
        if( unzGetCurrentFileInfo( uzf,
          &archiveFileInfo,
          NULL, 0,
          NULL, 0,
          NULL, 0 ) == UNZ_OK )
        {
          string archiveFileContents;
          uLong archiveFileSize = archiveFileInfo.uncompressed_size;
          unsigned long maxBufferSize = -1;
          if( archiveFileSize <= maxBufferSize )
          {
            archiveFileContents.resize( archiveFileSize );
            voidp archiveFileBuffer = &archiveFileContents[0];
            if( unzReadCurrentFile( uzf, archiveFileBuffer, archiveFileSize ) )
            {
              return archiveFileContents;
            }
          }
        }
      }
      error = 1;
      return "";
    }
    if( error != 0 ) return "";
    fileAccessStatus = unzGoToNextFile( uzf );
  }
  error = 2;
  return "";
}

vector<string> loadSharedStrings( xmlNode* ss )
{
  vector<string> result;
  xmlNode* parent = ss->getChild(1);
  size_t childC = parent->childCount();
  for( size_t it = 0; it < childC; it++ )
  {
    xmlNode* sharedString = parent->getChild(it)->getChild(0);
    result.push_back( sharedString->getContent(0) );
  }
  return result;
}

void decodeDimension( string dimension, unsigned &x, unsigned &y )
{
  int width = 0;
  size_t lcount = 0;
  while( dimension[lcount] >= 'A' && dimension[lcount] <= 'Z' ) lcount++;
  string letters = dimension.substr( 0, lcount );
  int value;
  for( size_t count = 0; count < lcount; count++ )
  {
    width *= 26;
    value = letters[count] + 1 - 'A';
    width += value;
  }
  string numbers = dimension.substr( lcount );
  x = width;
  convertData( numbers, y );
}

sheet loadWorksheet( string title, xmlNode* ws, const vector<string> &ss )
{
  xmlNode* top = ws->getChild(1);
  size_t child = 0;
  xmlNode* bottom = top->getChild(0);
  while( bottom->getName() != "dimension" )
  {
    child++;
    bottom = top->getChild(child);
  }
  vector<string> dimensions = bottom->getAttribute(0);
  unsigned cols; unsigned rows;
  decodeDimension( dimensions[1].substr( dimensions[1].find(':') + 1),
      cols, rows );
  sheet result( rows, cols, title );
  while( bottom->getName() != "sheetData" )
  {
    child++;
    bottom = top->getChild(child);
  }
  for( size_t row = 0; row < bottom->childCount(); row++ )
  {
    xmlNode* rowHead = bottom->getChild(row);
    for( size_t col = 0; col < rowHead->childCount(); col++ )
    {
      xmlNode* colHead = rowHead->getChild(col);
      unsigned cellX; unsigned cellY;
      decodeDimension( colHead->getAttribute(0)[1], cellX, cellY );
      cellX--; cellY--;
      string typeString = colHead->getAttribute(2)[1];
      string value = colHead->getChild(0)->getContent(0);
      if( typeString == "s" )
      {
          size_t index;
          convertData( value, index );
          writeCell( result, cellY, cellX, ss[index] );
      }
      else writeCell( result, cellY, cellX, value );
    }
  }
  return result;
}

vector<sheet> readXLSX( string filename, unsigned &error )
{
  vector<sheet> result;
  unzFile uzf = unzOpen( filename.c_str() );
  if( uzf )
  {
    vector<string> commonStrings;
    string sharedStringsContent = loadArchiveFile( uzf, "xl/sharedStrings.xml",
        error );
    if( error != 0 ) return result;
    xmlNode* sharedStringsXML = new xmlNode( "sharedstrings" );
    xmlDocument( sharedStringsXML, sharedStringsContent, error );
    if( error != 0 ) return result;
    vector<string> sharedStringList = loadSharedStrings( sharedStringsXML );
    if( error != 0 ) return result;
    string worksheetPath = "xl/worksheets/";
    vector<string> archiveFiles = getArchiveFiles( uzf, error );
    if( error != 0 ) return result;
    for( vector<string>::iterator it = archiveFiles.begin();
        it != archiveFiles.end(); it++ )
    {
      string filepath = it->substr( 0, it->find_last_of('/')+1 );
      if( filepath == worksheetPath )
      {
        string contents = loadArchiveFile( uzf, *it, error );
        if( error != 0 ) return result;
        xmlNode* worksheetXML = new xmlNode( "worksheet" );
        xmlDocument( worksheetXML, contents, error );
        if( error != 0 ) return result;
        string title = filename.substr( filename.find_last_of('/')+1 );
        title = title.substr( 0, title.find_last_of('.') );
        sheet worksheet = loadWorksheet( title, worksheetXML, sharedStringList );
        result.push_back( worksheet );
      }
    }
    return result;
  }
  error = 1;
  return result;
}

////////////////////////////////////////////////////////////////////////////////
////////  CORE FUNCTIONS
////////////////////////////////////////////////////////////////////////////////

FileType getFileType( string filename )
{
  size_t pos = filename.find_last_of( '.' ); 
  if( pos == string::npos ) return UNDEF;
  string file = toUpper( filename.substr( pos + 1 ) );
  if( file.compare( "CSV" ) == 0 ) return CSV;
  if( file.compare( "ODS" ) == 0 ) return ODS;
  if( file.compare( "XLSX" ) == 0 ) return XLSX;
  return UNDEF;
}

vector<sheet> readFile( string filename, unsigned &error )
{
  FileType filetype = getFileType( filename );
  vector<sheet> result;
  switch( filetype )
  {
    case CSV:
      result.push_back( readCSV( filename, error ) );
      return result;
      break;
    case XLSX:
      return readXLSX( filename, error );
      break;
    case ODS:
    default:
      error = 1;
      return result;
      break;
  }
}

////////////////////////////////////////////////////////////////////////////////
////////  TESTING
////////////////////////////////////////////////////////////////////////////////

// void testUtilities( ostream &out, unsigned &testsRun, unsigned &testsPassed,
//    unsigned ind = 0 )
// {
//   testsRun = 0;
//   testsPassed = 0;
//   //Begin testing
//   out << tab(ind) << "Initiating Utility Function Testing Protocols:" << '\n';
//   ind++;
//   function<int(int)> fAdd = add;
//   testFunction<int,int>( fAdd, "add", out, ind );
//   function<string(string)> fToUpper = toUpper;
//   testsRun++;
//   if( testFunction( fToUpper, "toUpper", out, ind ) ) testsPassed++;
//   ind--;
//   //End of tests
// }
// 
// void testCore( ostream &out, unsigned &testsRun, unsigned &testsPassed,
//    unsigned ind = 0 )
// {
//   testsRun = 0;
//   testsPassed = 0;
//   //Begin testing
//   out << tab(ind) << "Initiating Core Function Testing Protocols:" << '\n';
//   ind++;
//   function<FileType(string)> fGetFileType = getFileType;
//   testsRun++;
//   if( testFunction( fGetFileType, "getFileType", out, ind ) ) testsPassed++;
//   //End of tests
//   ind--;
// }
// 

void printSheet( sheet sh, ostream &out, unsigned ind )
{
  size_t rows = sh.rows();
  size_t cols = sh.cols();
  for( size_t row = 0; row < rows; row++ )
  {
    out << tab(ind);
    for( size_t col = 0; col < cols; col++ )
    {
      JType celltype = sh[row][col].getType();
      switch( celltype )
      {
        case STRING:
          out << "[S:" << sh[row][col].getString() << "]";
          break;
        case BOOL:
          out << "[B:" << sh[row][col].getBool() << "]";
          break;
        case NUMBER:
          out << "[N:" << sh[row][col].getNumber() << "]";
          break;
        default:
          out << "[NULL]";
          break;
      }
    }
    out << '\n';
  }
}

