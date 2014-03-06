#ifndef FILEREADER
#define FILEREADER

#include <string>
#include <vector>
using namespace std;

enum FileType { CSV, XLSX, UNDEF, ODS };
enum JType { STRING, NUMBER, OBJECT, ARRAY, BOOL, NULLVALUE };

//TODO: Fix value storage; only data of one type (string, number, and
//boolean) needs to be stored for any given instantiation, making it
//inefficient to allocate memory for all of them at once
class sheetNode
{
  public:
    sheetNode();
    sheetNode( string newString );
    sheetNode( double newNumber );
    sheetNode( bool newBool );
    JType getType();
    string getString();
    double getNumber();
    bool getBool();
  private:
    JType jType;
    string strval;
    double numval;
    bool boolval;
};

typedef struct
{
  string name;
  vector< vector<sheetNode> > contents;
} page;
 
vector< page > getFile( string fileName );

#endif
