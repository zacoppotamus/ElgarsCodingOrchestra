#include <iostream>
#include <vector>
#include <cstdio>
#include <cstring>
#include "JSONWriter.h"
#include "readFile.h"
#include "processTables.h"

using namespace std;

int main( int argc, char * argv[] )
{
  string filename;
  if( argc == 1 )
  {
    cout << "Usage: sadparser [filenames]" << '\n';
    return 0;
  }
  else if( argc == 2 )
  {
    if( strcmp( argv[1], "help" ) == 0 )
    {
      cout << "Usage: sadparser [filenames]" << '\n';
      return 0;
    }
  }
  for( int cfile = 1; cfile < argc; cfile++ )
  {
    filename = argv[cfile];
    cout << "Getting contents..." << '\n';
    vector< page > fileContents( getFile( filename ) );
    if( fileContents.size() > 0 )
    {
      cout << "Processing contents..." << '\n';
      vector< vector< vector<JSONObject> > > processedDataSheets;
      for(
        vector< page >::iterator it = fileContents.begin();
        it != fileContents.end();
        it++
      )
      {
        vector< vector<JSONObject> > processedData(
          processData( it->contents ) );
        processedDataSheets.push_back( processedData );
      }
      cout << "Printing contents..." << '\n';
      unsigned count = 0;
      unsigned pagecount = 0;
      for(
        vector< vector< vector<JSONObject> > >::iterator outIt =
          processedDataSheets.begin();
        outIt != processedDataSheets.end();
        outIt++
      )
      {
        string title = fileContents[pagecount].name;
        for(
          vector< vector<JSONObject> >::iterator inIt = outIt->begin();
          inIt != outIt->end();
          inIt++
        )
        {
          char * newFilename = new char[title.size()+8];
          sprintf( newFilename, "%s-%02d.json", title.c_str(), count+1 );
          string filetitle( newFilename );
          cout << filetitle << " created." << '\n';
          createJDocument( filetitle, *inIt );
          count++;
        }
        pagecount++;
      }
    }
    else
    {
      cout << '"' << filename << """ is an invalid file." << '\n';
    }
  }
  cout << "All done." << '\n';
  return 0;
}