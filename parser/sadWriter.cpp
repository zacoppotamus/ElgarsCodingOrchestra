#include <vector>
#include <fstream>
#include <sstream>
#include <string>
#include "sadWriter.hpp"
#include "sadReader.hpp"
#include "sadTables.hpp"

using namespace std;

string jsonDocument( sheet &sh, table tbl )
{
  vector<string> fieldNames;
  string output;
  output += "[\n";
  for( size_t col = tbl.x1; col <= tbl.x2; col++ )
  {
    fieldNames.push_back( sh[tbl.y1][col].getString() );
  }
  for( size_t row = tbl.y1+1; row <= tbl.y2; row++ )
  {
    output += "{\n";
    for( size_t col = tbl.x1; col <= tbl.x2; col++ )
    {
      output += "\t\"";
      output += fieldNames[col-tbl.x1];
      output += "\" : ";
      JType type = sh[row][col].getType();
      ostringstream converter;
      switch( type )
      {
        case STRING:
          output += "\"";
          output += sh[row][col].getString();
          output += "\"";
          break;
        case NUMBER:
          converter << sh[row][col].getNumber();
          output += converter.str();
          break;
        case BOOL:
          if( sh[row][col].getBool() ) output += "true";
          else output += "false";
          break;
        case NULLVALUE:
          output += "null";
          break;
        default:
          break;
      }
      output += '\n';
    }
    output += "}\n";
  }
  output += "]\n";
  return output;
}

void jsonFile( string filename, sheet &sh, vector<table> tbls )
{
  ofstream ofs( filename );
  for( vector<table>::iterator it = tbls.begin(); it != tbls.end(); it++ )
  {
    ofs << jsonDocument( sh, *it );
  }
}

