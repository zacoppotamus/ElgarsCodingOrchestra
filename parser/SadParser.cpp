#include <iostream>
#include <vector>
#include <cstdio>
#include "JSONWriter.h"
#include "readFile.h"
#include "processTables.h"

using namespace std;

int main( int argc, char * argv[] )
{
  string filename;
  if( argc == 1 )
  {
    cout << "Usage: sadparser [filename]" << "\n";
    return 0;
  }
  if( argc != 2 )
  {
    cout << "Invalid arguments." << "\n";
    return 0;
  }

  filename = argv[1];
  string title( filename );
  title.erase( title.find_last_of('.') );
  vector< vector<sheetNode> > fileContents( getFile( filename ) );
  vector< vector<JSONObject> > processedData( processData( fileContents ) );
  for( int count = 0; count < processedData.size(); count++ )
  {
    char * newFilename = new char[title.size()+8];
    sprintf( newFilename, "%s%02d.jso", title.c_str(), count+1 );
    string filetitle( newFilename );
    cout << filetitle << " created." << "\n";
    createJDocument( filetitle, processedData[count] );
  }
  cout << "All done." << "\n";
  return 0;
}
