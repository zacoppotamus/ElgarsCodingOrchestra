#ifndef SAD_TABLES
#define SAD_TABLES

struct item
{
  unsigned header;
  unsigned edge[4];
  unsigned corner[4];
  unsigned tuple;
  unsigned vbridge[3];
  bool hbridge;
};

struct trow
{
  long int mid;
  long int bot;
};

struct table
{
  size_t x1;
  size_t x2;
  size_t y1;
  size_t y2;
  long int score;
};

struct tableRating
{
  long int score;
  size_t depth;
};

std::vector<table> getSheetTables( sheet sh );

#endif
