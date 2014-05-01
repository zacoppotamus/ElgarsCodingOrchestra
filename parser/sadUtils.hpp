#ifndef SAD_UTILS
#define SAD_UTILS

#include <string>
#include <sstream>
#include <fstream>
#include <vector>
#include <functional>

extern const char* TESTING_PATH;
const unsigned MAX_READ_SIZE = 256;

std::string tab( unsigned size, char tabc = ' ', unsigned tabw = 2 );
std::string toUpper( std::string input );
bool whitespace( char c );

// Templated Functions

bool convertBool( std::string &data, bool &v );

// Generic string-to-data function
// Only works with data types that can be converted by a stringstream
template<class V>
bool convertData( std::string &data, V &v )
{
  std::istringstream ss(data);
  ss >> v;
  if( ss.fail() || !ss.eof() ) return false;
  else return true;
}

// Generic testing function
template <class Ret, class... Args>
bool testFunction( std::function<Ret(Args...)> f, std::string fname,
    std::ostream &out, unsigned ind )
{
  int argc = sizeof...(Args);
  out << tab(ind) << "Testing function " << fname << "..." << '\n';
  if( argc > 5 )
  {
    out << tab(ind) << "ERROR: Too many arguments to function." << '\n';
    return false;
  }
  if( argc < 1 )
  {
    out << tab(ind) << "ERROR: Too few arguments to function." << '\n';
    return false;
  }
  return runFunctionTests( f, fname, out, ind );
}

#endif

