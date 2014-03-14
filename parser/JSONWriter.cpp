#include "JSONWriter.h"
#include <iostream>
#include <fstream>
#include <vector>
#include <string>
#include <sstream>

using namespace std;

/////////////////////////////////////////////////////
// JSONObject Implementation

JSONObject::JSONObject()
{
  fields = 0;
}

int JSONObject::fieldCount()
{
  return fields;
}

string JSONObject::getName( unsigned pos )
{
  if( pos >= 0 && pos < fields )
  {
    return names[pos];
  }
  else
  {
    return NULL;
  }
}

string JSONObject::getValue( unsigned pos )
{
  if( pos >= 0 && pos < fields )
  {
    return values[pos];
  }
  else
  {
    return NULL;
  }
}

void JSONObject::addPair( string name )
{
  names.push_back(name);
  values.push_back("null");
  fields++;
}

void JSONObject::addPair( string name, double value )
{
  names.push_back(name);
  ostringstream converter;
  converter << value;
  values.push_back(converter.str());
  fields++;
}

void JSONObject::addPair( string name, const char * value )
{
  string stringValue = value;
  addPair( name, stringValue );
}

void JSONObject::addPair( string name, string &value )
{
  names.push_back(name);
  values.push_back(value);
  fields++;
}

void JSONObject::addPair( string name, bool value )
{
  names.push_back(name);
  if( value )
  {
    values.push_back("true");
  }
  else
  {
    values.push_back("false");
  }
  fields++;
}

// End of JSONObject Implementation
/////////////////////////////////////////////////////

string JSONString( JSONObject object )
{
  string document = "{\n";
  unsigned count = 0;
  unsigned max = object.fieldCount();
  while( count < max )
  {
    document += "\t";
    document += "\"";
    document += object.getName(count);
    document += "\": \"";
    document += object.getValue(count);
    document += "\"";
    count++;
    if( count < max )
    {
      document += ",";
    }
    document += '\n';
  }
  document += "}\n";
  return document;
}

//Inputs:
//  string name:                A path and filename for the new file in which
//                              the JSON document will be stored.
//  vector<JSONObject> objects: A vector containing the JSON objects that the
//                              new document contains.
//Output: An integer representing the success of the operation. Values are:
//  0: Successful operation
//  1: File I/O error
int createJDocument( string name, vector<JSONObject> objects )
{
  ofstream ofs( name );
  if( !ofs.good() )
  {
    return 1;
  }
  for( vector<JSONObject>::iterator it = objects.begin();
    it != objects.end();
    it++ )
  {
    ofs << JSONString( *it );
  }
  ofs.close();
  return 0;
}
