#include <iostream>
#include <string>
#include <vector>
#include <fstream>
#include <sstream>
#include <typeinfo>
#include <functional>
#include "sadUtils.hpp"
#include "sadReader.hpp"

const char TEST_DELIM = '#';
const char* TESTING_PATH = "tests";

using namespace std;

////////////////////////////////////////////////////////////////////////////////
////////  TEXT FUNCTIONS
////////////////////////////////////////////////////////////////////////////////

bool convertBool( std::string &data, bool &v )
{
  std::string input = toUpper( data );
  if( input == "TRUE" )
  {
    v = true;
    return true;
  }
  if( input == "FALSE" )
  {
    v = false;
    return true;
  }
  return false;
}

bool whitespace( char c )
{
  if( c == ' '  || c == '\n' || c == '\r' || c == '\t' ) return true;
  return false;
}

string toUpper( string input )
{
  string output = input;
  for( string::iterator it = output.begin(); it != output.end(); ++it )
  {
    if( (*it >= 'a') && (*it <= 'z') )
    {
      *it += 'A' - 'a';
    }
  }
  return output;
}

string tab( unsigned size, char tabc, unsigned tabw )
{
  string output;
  for( unsigned it = 0; it < size*tabw; it++ ) output += tabc;
  return output;
}

////////////////////////////////////////////////////////////////////////////////
////////  TESTING FUNCTIONS
////////////////////////////////////////////////////////////////////////////////

// All test files should be named after their function (i.e. the test for the
// function "increment" should also be "increment") and must consist of the 
// following format:
// #
// <Test Data A1>#<Test Data A2>#...#<Test Data An>
// <Test Data B1>#<Test Data B2>#...#<Test Data Bn>
// ...
// #
// <Test Result A>
// <Test Result B>
// ...
// #
//
// Where '#' = TEST_DELIM, and must not contain any other instances symbols.
//
// For loading test data, the "error" argument is set to the result of the
// attempt:
// 0: Success
// 1: File I/O error
// 2: Invalid test file
// 3: EOF reached early

template <class V>
struct LoadTest
{
  vector<V> operator() ( string fname, unsigned index, unsigned &error,
      unsigned &pof )
  {
    pof = 0;
    char line[MAX_READ_SIZE];
    vector<V> output;
    string filepath = TESTING_PATH;
    filepath += "/" + fname;
    // Simple file-access stuff
    ifstream ifs ( filepath.c_str() );
    if( !ifs.good() )
    {
      error = 1;
      return output;
    }
    ifs.getline( line, MAX_READ_SIZE ); pof++;
    if( ifs.good() )
    {
      // Verifying that the file matches the format
      if( line[0] != TEST_DELIM )
      {
        error = 2;
        return output;
      }
      // Loops through lines of test data
      ifs.getline( line, MAX_READ_SIZE ); pof++;
      while( line[0] != TEST_DELIM && ifs.good() )
      {
        //Extract the indexed data into a string
        string data (line);
        size_t pos = 0;
        for( unsigned i = 0; i < index; i++ )
        {
          pos = data.find( TEST_DELIM, pos );
          if( pos == string::npos )
          {
            error = 2;
            return output;
          }
          pos++;
        }
        data = data.substr( pos );
        pos = data.find( TEST_DELIM );
        if( pos != string::npos ) data = data.substr( 0, pos );
        // The magic happens...
        V next;
        if( !convertData( data, next ) )
        {
          error = 2;
          return output;
        }
        output.push_back( next );
        ifs.getline( line, MAX_READ_SIZE ); pof++;
      }
      if( !ifs.fail() && line[0] == TEST_DELIM  )
      {
        error = 0;
        return output;
      }
    }
    if( ifs.fail() ) error = 1;
    else error = 3;
    return output;
  }
};

template <class V>
struct LoadResult
{
  vector<V> operator() ( string fname, unsigned &error, unsigned &pof )
  {
    pof = 0;
    char line[MAX_READ_SIZE];
    vector<V> output;
    string filepath = TESTING_PATH;
    filepath += "/" + fname;
    // Simple file-access stuff
    ifstream ifs ( filepath.c_str() );
    if( !ifs.good() )
    {
      error = 1;
      return output;
    }
    ifs.getline( line, MAX_READ_SIZE ); pof++;
    if( ifs.good() )
    {
      // Verifying that the file matches the format
      if( line[0] != TEST_DELIM )
      {
        error = 2;
        return output;
      }
      // Loops through all of the test data, doing nothing
      ifs.getline( line, MAX_READ_SIZE ); pof++;
      while( line[0] != TEST_DELIM && ifs.good() )
      {
        ifs.getline( line, MAX_READ_SIZE ); pof++;
      }
      if( ifs.good() )
      {
        // Loops through all of the test results, adding them to the output 
        ifs.getline( line, MAX_READ_SIZE ); pof++;
        while( line[0] != '#' && ifs.good() )
        {
          V next;
          string data = line;
          if( !convertData( data, next ) )
          {
            error = 2;
            return output;
          }
          output.push_back( next );
          ifs.getline( line, MAX_READ_SIZE ); pof++;
        }
        // Confirm there are no stream errors, with the exception of EOF
        if( !ifs.fail() )
        {
          error = 0;
          return output;
        }
      }
    }
    if( ifs.fail() ) error = 1;
    else error = 3;
    return output;
  }
};

template <class T>
vector<T> getData( LoadResult<T> loadResult, string fname, unsigned index,
    ostream &out, unsigned ind, unsigned &error )
{
  unsigned pof;
  vector<T> data = loadResult( fname, error, pof );
  if( error != 0 )
  {
    switch( error )
    {
      case 1:
        if( pof == 0 )
        {
          out << tab(ind) << "ERROR: I/O error while trying to open test "
            << " file." << '\n';
        }
        else
        {
          out << tab(ind) << "ERROR: I/O error occured on line " << pof
            << " of test file." << '\n';
        }
        break;
      case 2:
        out << tab(ind) << "ERROR: Invalid input on line " << pof
          << " of test file." << '\n';
        break;
      case 3:
        out << tab(ind) << "ERROR: End of file reached early." << '\n';
        break;
      // Should not conceivably happen
      default:
        out << tab(ind) << "ERROR: Unknown error code received while "
          << " reading test file." << '\n';
        break;
    } 
  }
  return data;
}

template <class T>
vector<T> getData( LoadTest<T> loadTest, string fname, unsigned index,
    ostream &out, unsigned ind, unsigned &error )
{
  unsigned pof;
  vector<T> data = loadTest( fname, index, error, pof );
  if( error != 0 )
  {
    switch( error )
    {
      case 1:
        if( pof == 0 )
        {
          out << tab(ind) << "ERROR: I/O error while trying to open test "
            << " file." << '\n';
        }
        else
        {
          out << tab(ind) << "ERROR: I/O error occured on line " << pof
            << " of test file." << '\n';
        }
        break;
      case 2:
        out << tab(ind) << "ERROR: Invalid input on line " << pof
          << " of test file." << '\n';
        break;
      case 3:
        out << tab(ind) << "ERROR: End of file reached early." << '\n';
        break;
      // Should not conceivably happen
      default:
        out << tab(ind) << "ERROR: Unknown error code received while "
          << " reading test file." << '\n';
        break;
    } 
  }
  return data;
}

template <class Ret, class A>
bool runFunctionTests( function<Ret(A)> f, string fname, ostream &out,
    unsigned ind )
{
  // Obtains the test data
  unsigned error;
  LoadResult<Ret> loadResult;
  vector<Ret> resultData = getData( loadResult, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<A> loadTestA;
  vector<A> testA = getData( loadTestA, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  // Checks the volume of test data for validity
  vector<size_t> failures;
  size_t size = resultData.size();
  if( size != testA.size() )
  {
    out << tab(ind) << "ERROR: Different test and result pool sizes." << '\n';
    return false;
  }
  vector<Ret> testResults;
  for( size_t it = 0; it < size; it++ )
  {
    testResults.push_back( f(testA[it]) );
  }
  for( size_t it = 0; it < size; it++ )
  {
    if( resultData[it] != testResults[it] ) failures.push_back( it );
  }
  if( failures.size() == 0 )
  {
    out << tab(ind) << "SUCCESS: All " << size << "  tests passed." << '\n';
    return true;
  }
  out << tab(ind) << "FAILURE: Function failed the following tests:" << '\n';
  ind++;
  for( vector<size_t>::iterator it = failures.begin(); it != failures.end();
      it++ )
  {
    out << tab(ind) << *it << '\n';
  }
  ind--;
  return false;
}

template <class Ret, class A, class B>
bool runFunctionTests( function<Ret(A,B)> f, string fname, ostream &out,
    unsigned ind )
{
  // Obtains the test data
  unsigned error;
  LoadResult<Ret> loadResult;
  vector<Ret> resultData = getData( loadResult, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<A> loadTestA;
  vector<A> testA = getData( loadTestA, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<B> loadTestB;
  vector<B> testB = getData( loadTestB, fname, 1, out, ind, error );
  if( error != 0 ) return false;
  // Checks the volume of test data for validity
  vector<size_t> failures;
  size_t size = resultData.size();
  if( size != testA.size() )
  {
    out << tab(ind) << "ERROR: Different test and result pool sizes." << '\n';
    return false;
  }
  vector<Ret> testResults;
  for( size_t it = 0; it < size; it++ )
  {
    testResults.push_back( f(testA[it],testB[it]) );
  }
  for( size_t it = 0; it < size; it++ )
  {
    if( resultData[it] != testResults[it] ) failures.push_back( it );
  }
  if( failures.size() == 0 )
  {
    out << tab(ind) << "SUCCESS: All " << size << "  tests passed." << '\n';
    return true;
  }
  out << tab(ind) << "FAILURE: Function failed the following tests:" << '\n';
  ind++;
  for( vector<size_t>::iterator it = failures.begin(); it != failures.end();
      it++ )
  {
    out << tab(ind) << *it << '\n';
  }
  ind--;
  return false;
}

template <class Ret, class A, class B, class C>
bool runFunctionTests( function<Ret(A,B,C)> f, string fname, ostream &out,
    unsigned ind )
{
  // Obtains the test data
  unsigned error;
  LoadResult<Ret> loadResult;
  vector<Ret> resultData = getData( loadResult, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<A> loadTestA;
  vector<A> testA = getData( loadTestA, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<B> loadTestB;
  vector<B> testB = getData( loadTestB, fname, 1, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<C> loadTestC;
  vector<C> testC = getData( loadTestC, fname, 2, out, ind, error );
  if( error != 0 ) return false;
  // Checks the volume of test data for validity
  vector<size_t> failures;
  size_t size = resultData.size();
  if( size != testA.size() )
  {
    out << tab(ind) << "ERROR: Different test and result pool sizes." << '\n';
    return false;
  }
  vector<Ret> testResults;
  for( size_t it = 0; it < size; it++ )
  {
    testResults.push_back( f(testA[it],testB[it],testC[it]) );
  }
  for( size_t it = 0; it < size; it++ )
  {
    if( resultData[it] != testResults[it] ) failures.push_back( it );
  }
  if( failures.size() == 0 )
  {
    out << tab(ind) << "SUCCESS: All " << size << "  tests passed." << '\n';
    return true;
  }
  out << tab(ind) << "FAILURE: Function failed the following tests:" << '\n';
  ind++;
  for( vector<size_t>::iterator it = failures.begin(); it != failures.end();
      it++ )
  {
    out << tab(ind) << *it << '\n';
  }
  ind--;
  return false;
}

template <class Ret, class A, class B, class C, class D >
bool runFunctionTests( function<Ret(A,B,C,D)> f, string fname, ostream &out,
    unsigned ind )
{
  // Obtains the test data
  unsigned error;
  LoadResult<Ret> loadResult;
  vector<Ret> resultData = getData( loadResult, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<A> loadTestA;
  vector<A> testA = getData( loadTestA, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<B> loadTestB;
  vector<B> testB = getData( loadTestB, fname, 1, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<C> loadTestC;
  vector<C> testC = getData( loadTestC, fname, 2, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<D> loadTestD;
  vector<D> testD = getData( loadTestD, fname, 3, out, ind, error );
  if( error != 0 ) return false;
  // Checks the volume of test data for validity
  vector<size_t> failures;
  size_t size = resultData.size();
  if( size != testA.size() )
  {
    out << tab(ind) << "ERROR: Different test and result pool sizes." << '\n';
    return false;
  }
  vector<Ret> testResults;
  for( size_t it = 0; it < size; it++ )
  {
    testResults.push_back( f(testA[it],testB[it],testC[it],testD[it]) );
  }
  for( size_t it = 0; it < size; it++ )
  {
    if( resultData[it] != testResults[it] ) failures.push_back( it );
  }
  if( failures.size() == 0 )
  {
    out << tab(ind) << "SUCCESS: All " << size << "  tests passed." << '\n';
    return true;
  }
  out << tab(ind) << "FAILURE: Function failed the following tests:" << '\n';
  ind++;
  for( vector<size_t>::iterator it = failures.begin(); it != failures.end();
      it++ )
  {
    out << tab(ind) << *it << '\n';
  }
  ind--;
  return false;
}

template <class Ret, class A, class B, class C, class D, class E>
bool runFunctionTests( function<Ret(A,B,C,D,E)> f, string fname, ostream &out,
    unsigned ind )
{
  // Obtains the test data
  unsigned error;
  LoadResult<Ret> loadResult;
  vector<Ret> resultData = getData( loadResult, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<A> loadTestA;
  vector<A> testA = getData( loadTestA, fname, 0, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<B> loadTestB;
  vector<B> testB = getData( loadTestB, fname, 1, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<C> loadTestC;
  vector<C> testC = getData( loadTestC, fname, 2, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<D> loadTestD;
  vector<D> testD = getData( loadTestD, fname, 3, out, ind, error );
  if( error != 0 ) return false;
  LoadTest<E> loadTestE;
  vector<E> testE = getData( loadTestE, fname, 4, out, ind, error );
  if( error != 0 ) return false;
  // Checks the volume of test data for validity
  vector<size_t> failures;
  size_t size = resultData.size();
  if( size != testA.size() )
  {
    out << tab(ind) << "ERROR: Different test and result pool sizes." << '\n';
    return false;
  }
  vector<Ret> testResults;
  for( size_t it = 0; it < size; it++ )
  {
    testResults.push_back(
        f(testA[it],testB[it],testC[it],testD[it],testE[it]) );
  }
  for( size_t it = 0; it < size; it++ )
  {
    if( resultData[it] != testResults[it] ) failures.push_back( it );
  }
  if( failures.size() == 0 )
  {
    out << tab(ind) << "SUCCESS: All " << size << "  tests passed." << '\n';
    return true;
  }
  else
  {
    out << tab(ind) << "FAILURE: Function failed the following tests:" << '\n';
    ind++;
    for( vector<size_t>::iterator it = failures.begin(); it != failures.end();
        it++ )
    {
      out << tab(ind) << *it << '\n';
    }
    ind--;
  }
  return false;
}

