#include <iostream>
#include <vector>
#include <cstdio>
#include <cstring>
#include "sadReader.hpp"
#include "sadTables.hpp"
#include "sadWriter.hpp"

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
    if( filename[0] == '-' )
    {
      if( filename.compare("-n") == 0 )
      {
        //Placeholder text
        continue;
      }
      else
      {
        cout << "Flag not recognized: \"" << filename << "\"" << '\n';
        continue;
      }
    }
    cout << "Getting contents..." << '\n';
    unsigned error = 0;
    vector<sheet> fileContents = readFile( filename, error );
    if( error == 0 )
    {
      cout << "Processing contents..." << '\n';
      vector< vector<table> > tablesOfSheets;
      for(
        vector<sheet>::iterator it = fileContents.begin();
        it != fileContents.end();
        it++
      )
      {
        vector<table> tableList = getSheetTables( *it );
        tablesOfSheets.push_back( tableList );
      }
      cout << "Printing contents..." << '\n';
      unsigned count = 0;
      for( size_t it = 0; it < fileContents.size(); it++ )
      {
        string title = fileContents[it].name();
        char * newFilename = new char[title.size()+10];
        sprintf( newFilename, "%s-%02d.json", title.c_str(), count+1 );
        string filetitle( newFilename );
        jsonFile( filetitle, fileContents[it], tablesOfSheets[it] );
        cout << filetitle << " created." << '\n';
        count++;
      }
    }
    else
    {
      switch( error )
      {
        case 1:
          cout << "ERROR: Failure in file access operations." << '\n';
          break;
        case 2:
        case 3:
          cout << "ERROR: Invalid file contents." << '\n';
          break;
        default:
          cout << "ERROR: Unknown error code, something is wrong." << '\n';
          break;
      }
    }
  }
  cout << "All done." << '\n';
  return 0;
}

