#include "json_spirit.h"
#include <iostream>
#include <fstream>
#include <cstdlib>
#include <stdarg.h>
#include <string>
#include <vector>

using namespace json_spirit;
using namespace std;

enum e_type{ arg_error };

//////////////////////////
//Record class and methods
class record
{
  public:
    record( int field_count );
    ~record();

    void add_item( Value value );
    void add_item( Value value, int pos );
    void change_type( Value_type new_type, int pos );
    Value_type get_type( int pos );
    Value get_value( int pos );
  private:
    int field_count;
    int item_count;
    Value* fields;
};

record::record( int field_count )
{
  item_count = 0;
  this->field_count = field_count;
  fields = new Value[field_count];
  for( int i = 0; i < field_count; i++ )
    fields[i] = new Value();
}

record::~record()
{
  delete fields;
}

void record::add_item( Value value )
{
  if( item_count < field_count )
    if( fields[item_count].type() == null_type )
    {
        fields[item_count] = value;
        item_count++;
    }
}

void record::add_item( Value value, int pos )
{
  if( fields[pos].type() == null_type )
  {
    fields[pos] = value;
    item_count++;
  }
}

void record::change_type( Value_type new_type, int pos )
{
  if( new_type == fields[pos].type() ) return;
  if( new_type == str_type )
  {
    if( fields[pos].type() == bool_type )
    {
      if( fields[pos].get_bool() == 1 )
        fields[pos] = Value("Yes");
      else
        fields[pos] = Value("No");
    }
    else
      if( fields[pos].type() == int_type )
        fields[pos] = Value( to_string( fields[pos].get_int() ) );
      else
        fields[pos] = Value( to_string( fields[pos].get_real() ) );
  }
  else
}

Value_type record::get_type( int pos )
{
  return fields[pos].type();
}

Value record::get_value( int pos )
{
  return fields[pos];
}

////////////////////////////////
//record_style class and methods
class record_style
{
  public:
    record_style( int fields );
    ~record_style();
    
    Value_type get_type( int pos );
    void remove_field( int pos );
    int get_size();
    void change_type( Value_type new_type, int pos );
  private:
    int record_size;
    Value_type* field_types;
};

record_style::record_style( int fields )
{
  record_size = fields;
  field_types = new Value_type[fields];
}

record_style::~record_style()
{
  delete field_types;
}

void record_style::remove_field( int pos )
{
	if( pos >= 0 && pos <= record_size )
  {
		int i;
    for( i = pos; i < record_size; i++ )
      field_types[i] = field_types[i+1];
    record_size--;
  }
}

Value_type record_style::get_type( int pos )
{
	if( pos < 0 || pos > record_size )
		return null_type;
	else
		return field_types[pos];
}

int record_style::get_size()
{
	return record_size;
}

void record_style::change_type( Value_type new_type, int pos )
{
  //Brief explanation:
  //If the current type is null, it must be overwritten. 
  //If both types are the same, no change is made.
  //If either types are strings, the field must be a string.
  //Subsequently, as the old and new types must be different and non-strings,
  //if either is a boolean then the other is numerical therefore meaning the
  //field could only be a string.
  //If none of these conditions are met, then the types must be real and int,
  //in which case real takes precedence.
  if( new_type == null_type ) return;
  if( field_types[pos] == null_type )
  {
    field_types[pos] = new_type;
    return;
  }
  if( new_type == field_types[pos] )
  {
    field_types[pos] = new_type;
    return;
  }
  if( field_types[pos] == str_type || new_type == str_type )
  {
    field_types[pos] = str_type;
    return;
  }
  if( field_types[pos] == bool_type || new_type == bool_type )
  {
    field_types[pos] = str_type;
    return;
  }
  field_types[pos] =  real_type;
}

/////////////////////////
//table class and methods
class table
{
  public:
    table( int field_count, int record_count );
    ~table();
    void add_field( Value new_field );
    void add_item( int row, int col, Value new_item );
    void type_scan();
    void print_table( string filename );
  private:
    int field_lock;
    int field_count;
    int record_count;
    record header;
    record_style style;
    vector<record> records;
};

table::table( int field_count, int record_count ):
  header( field_count ),
  style( field_count ),
  records( record_count, record(field_count) )
{
  field_lock = 0;
  this->field_count = field_count;
  this->record_count = record_count;
}

table::~table()
{}

void table::add_field( Value new_field )
{
  if( field_lock < field_count )
  {
    header.add_item( new_field );
    field_lock++;
  }
}

void table::add_item( int row, int col, Value new_item )
{
  records[row].add_item( new_item, col );
}

void table::type_scan()
{
  for( int i = 0; i < field_count; i++ )
  {
    Value new_value;
    for( int j = 0; j < record_count; j++ )
    {
      new_value = records[j].get_type( i );
      style.change_type( new_value.type(), i );
    }
    for( int j = 0; j < record_count; j++ )
      records[j].change_type( style.get_type( i ), i );
  }
}

void table::print_table( string filename )
{
  ofstream output( filename );
  for( int i = 0; i < record_count; i++ )
  {
    Object tuple;
    for( int j = 0; j < field_count; j++ )
      tuple.push_back( Pair( header.get_value(j).get_str(),
                           records[i].get_value(j) ) );
    write( tuple, output, remove_trailing_zeros );
  }
  output.close();
}

//////////////
//main methods
void error( e_type err )
{
  cerr
    << "Invalid arguments given. Correct usage: ""./parser <filename>"", "
    << "where <filename> is a valid .cvs file. "
    << endl;
}

int boolean_value( string value )
{
  string bool_string = """Yes""";
  if( value.compare( bool_string ) == 0 ) return 1;
  bool_string = """No""";
  if( value.compare( bool_string ) == 0 ) return 0;
  return -1;
}

Value parse_value( string value )
{
  Value new_value;
  int i = 0;
  int length = value.size();
  size_t* pos;
  
  //Tests for the null type
  if( length == 0 ) return null_type;

  //Tests for the boolean type
  if( boolean_value( value ) != -1 )
  {
    if( boolean_value( value ) == 1 ) new_value = new Value( true );
    else new_value = new Value( false );
    return new_value;
  }
  
  //Tests for the real type
  try
  {
    double real_value = stod( value, pos );
    if( *pos == length )
    {
      new_value = new Value( real_value );
      return new_value;
    }
    else
    {
      new_value = new Value( value );
      return new_value;
    }
  }
  catch( exception e )
  {}
  
  //Tests for the integer type
  try
  {
    int int_value = stoi( value, pos );
    if( *pos == length )
    {
      new_value = new Value( int_value );
      return new_value;
    }
    else
    {
      new_value = new Value( value );
      return new_value;
    }
  }
  catch( exception e )
  {}

  //Confirms the string type
  new_value = new Value( substr( (size_t)1, value.length()-2 );
  return new_value;
}

int main( int argc, char** argv )
{
  if( argc != 2 )
  {
    e_type err = arg_error;
    error( err );
    return 0;
  }

  /////////////////////////////////////
  //FILE READING
  int count, count_2, count_3, count_4;
  ifstream input;
  string filename = argv[1];
  int total_width = 1;
  int total_height = 1;
  input.open( filename.c_str() );

  char c = ' ';
  while( c != '\n' && c != EOF )
  {
    if( c == ',' ) total_width++;
    c = input.get();
  }
  while( c != EOF )
  {
    if( c == '\n' ) total_height++;
    c = input.get();
  }

  Value** spreadsheet = new Value*[total_height];
  for( int i = 0; i < total_height; i++ )
    spreadsheet[i] = new Value[total_width];

  input.clear();
  input.seekg(0);
  string current_item;
  for( int i = 0; i < total_height; i++ )
    for( int j = 0; j < total_width; j++ )
    {
      current_item = string();
      c = input.get();
      while( c != ',' && c != '\n' )
      {
        current_item += c;
      }
      spreadsheet[i][j] = parse_value( current_item );
    }

  /////////////////////////////////////
  //MAKING TABLES
  count = 0;
  while( count < total_height )
  {
    count_2 = 0;
    while( count_2 < total_width )
    {
      if( spreadsheet[count][count_2].type() == str_type )
      {
        int record_count = 0;
        int field_count = 1;
        while( count_2 + field_count < total_width )
        {
          if( spreadsheet[count][count_2+field_count].type() != str_type )
            break;
          field_count++;
        }
        while( count + record_count + 1 < total_height )
        {
          if( spreadsheet[count+record_count+1][count_2].type() == null_type )
            break;
          record_count++;
        }
        if( record_count > 1 || field_count > 1 )
        {
          table new_table( field_count, record_count );
          count_3 = count;
          count_4 = count_2;
          for( count_4 = count_2; count_4 < count + field_count; count_4++ )
            new_table.add_field( spreadsheet[count][count_4] );
          count_3 = count + 1;
          count_4 = count_2;
          while( count_3 <= count + record_count )
          {
            while( count_4 < count_2 + field_count )
            {
              new_table.add_item( count_4-count_2, count_3-count+1,
                spreadsheet[count_3][count_4] );
              count_4++;
            }
            count_3++;
          }
          new_table.type_scan();
          new_table.print_table( filename+".txt" );

          count_3 = count;
          count_4 = count_2;
          while( count_3 <= count + record_count )
          {
            while( count_4 < count_2 + field_count )
            {
              spreadsheet[count_3][count_4] = Value();
              count_4++;
            }
            count_3++;
          }
        }
      }
      count_2++;
    }
    count++;
  }
  
  for( int i = 0; i < total_height; i++ )
    delete spreadsheet[i];
  delete[] spreadsheet;
  return 0;
}
