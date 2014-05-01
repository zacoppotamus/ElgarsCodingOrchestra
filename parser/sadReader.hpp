#ifndef FILE_READER
#define FILE_READER

#include <string>
#include <iostream>
#include <vector>

extern const char* SHARED_STRINGS_PATH;
const long unsigned MAX_BUFFER_SIZE = 65536;

enum FileType : unsigned {
  UNDEF = 0,
  CSV = 1,
  XLSX = 2,
  ODS = 3
};

enum JType : unsigned {
  NULLVALUE = 0,
  STRING = 1,
  NUMBER = 2,
  BOOL = 3,
  OBJECT = 4,
  ARRAY = 5
};

class xmlNode
{
  public:
    xmlNode( std::string input );
    void addAttribute( std::string attribute, std::string value );
    void addChild( xmlNode* child );
    void addContent( std::string input );
    size_t attributeCount();
    size_t childCount();
    size_t contentCount();
    std::vector<std::string> getAttribute( size_t index );
    xmlNode* getChild( size_t index );
    std::string getContent( size_t index );
    std::string getName();
  private:
    std::string name;
    size_t attC;
    std::vector<std::string> attributes;
    std::vector<std::string> values;
    size_t childC;
    std::vector<xmlNode*> children;
    size_t contentC;
    std::vector<std::string> content;
};


class cell
{
  public:
    cell();
    cell( std::string newString );
    cell( long double newNumber );
    cell( bool newBool );
    void setValue( std::string input );
    void setValue( long double input );
    void setValue( bool input );
    JType getType();
    std::string getString();
    long double getNumber();
    bool getBool();
  private:
    JType jType;
    std::string strval;
    long double numval;
    bool boolval;
};

// Access elements with sheet[x][y], where x is the row and y is the col
class sheet
{
  public:
    sheet( size_t rows, size_t columns, std::string name = "sheet" );
    std::vector<cell>& operator [](size_t idx) { return contents[idx]; }
    const std::vector<cell>& operator [](size_t idx) const
    {
      return contents[idx];
    }
    size_t rows();
    size_t cols();
    std::string name();
  private:
    std::string sName;
    size_t rowc;
    size_t colc;
    std::vector< std::vector<cell> > contents;
};

std::vector<sheet> readFile( std::string filename, unsigned &error );
void printSheet( sheet sh, std::ostream &out, unsigned ind = 0 );

#endif
