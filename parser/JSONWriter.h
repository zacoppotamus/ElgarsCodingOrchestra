#ifndef FILEWRITER
#define FILEWRITER

#include <vector>
#include <string>

using namespace std;

class JSONObject
{
  public:
    JSONObject();
    void addPair( string name, const char * value );
    void addPair( string name, string &value );
    void addPair( string name, double value );
    void addPair( string name, bool value );
    void addPair( string name );
    string getName( unsigned pos );
    string getValue( unsigned pos );
    int fieldCount();
  private:
    unsigned fields;
    std::vector<string> names;
    std::vector<string> values;
};

string JSONString( JSONObject object );
int createJDocument( string name, vector<JSONObject> objects );

#endif
