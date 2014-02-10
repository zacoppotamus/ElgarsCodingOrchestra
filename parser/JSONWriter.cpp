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

string JSONObject::getName( int pos )
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

string JSONObject::getValue( int pos )
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
  //Commented out testing protocols
  //cout << "Added null-value " << name << ":null.\n";
  names.push_back(name);
  values.push_back("null");
  fields++;
}

void JSONObject::addPair( string name, double value )
{
  //Commented out testing protocols
  //cout << "Added number-value " << name << ":" << value << ".\n";
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
  //Commented out testing protocols
  //cout << "Added string-value " << name << ":" << value << ".\n";
  names.push_back(name);
  values.push_back(value);
  fields++;
}

void JSONObject::addPair( string name, bool value )
{
  //Commented out testing protocols
  //cout << "Added bool-value " << name << ":" << value << ".\n";
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
  //Commented out testing protocols
  //cout << "\t\tInitiating JSON string generation tests...\n";
  string document = "{\n";
  int count = 0;
  int max = object.fieldCount();
  //cout << "\t\tBeginning name:value generation...\n";
  while( count < max )
  {
    //cout << "\t\tCreating pair " << count << "...\n";
    document += "\t";
    document += object.getName(count);
    document += ":";
    document += object.getValue(count);
    count++;
    if( count < max )
    {
      document += ",";
    }
    document += "\n";
  }
  //cout << "\t\tname:value generation complete...\n";
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
  //Commented out testing protocols
  //cout << "\tInitiating document generation tests...\n";
  int objectCount = objects.size();
  ofstream ofs( name );
  //cout << "\tStream generated...\n";
  if( !ofs.good() )
  {
    return 1;
  }
  //cout << "\tStream successfully opened...\n";
  for( vector<JSONObject>::iterator it = objects.begin();
    it != objects.end();
    it++ )
  {
    ofs << JSONString( *it );
  }
  //cout << "\tObjects written to stream...\n";
  ofs.close();
  //cout << "\tStream closed...\n";
  return 0;
}

/*
int main()
{
  cout << "Initiating test protocols...\n";
  vector<JSONObject> objects;
  cout << "Empty vector generated...\n";
  JSONObject a;
  a.addPair( "Name", "Alice" );
  a.addPair( "Age", 22.0 );
  a.addPair( "Married?", false );
  a.addPair( "Pointless Field" );
  cout << "Object a generated...\n";
  objects.push_back( a );
  cout << "Object a added to vector...\n";
  JSONObject b;
  b.addPair( "Name", "Bob" );
  b.addPair( "Age", 37.0 );
  b.addPair( "Married?", false );
  b.addPair( "Pointless Field" );
  objects.push_back( b );
  cout << "Object b generated...\n";
  JSONObject c;
  c.addPair( "Name", "Charlotte" );
  c.addPair( "Age", 26.0 );
  c.addPair( "Married?", true );
  c.addPair( "Pointless Field" );
  objects.push_back( c );
  cout << "Object c generated...\n";
  JSONObject d;
  d.addPair( "Name", "Daniel" );
  d.addPair( "Age", 16.0 );
  d.addPair( "Married?", true );
  d.addPair( "Pointless Field" );
  objects.push_back( d );
  cout << "Object d generated...\n";
  cout << "Beginning document creation...\n";
  int result = createJDocument( "testwrite.txt", objects );
  cout << "Document generated...\n";
  if( result == 0 )
  {
    cout << "All is good.\n";
  }
  else
  {
    cout << "IO error.\n";
  }
  return 0;
}
*/
