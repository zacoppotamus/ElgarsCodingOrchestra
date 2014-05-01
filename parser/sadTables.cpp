#include <string>
#include <vector>
#include "sadReader.hpp"
#include "sadUtils.hpp"
#include "sadTables.hpp"
#include "sadWriter.hpp"

using namespace std;

const unsigned edges[4][6] =
{
  { 0, 1, 2, 3, 4, 5 },
  { 3, 4, 5, 6, 7, 8 },
  { 0, 1, 3, 4, 6, 7 },
  { 1, 2, 4, 5, 7, 8 }
};

const unsigned corners[4][6] =
{
  { 0, 1, 3, 4 },
  { 1, 2, 4, 5 },
  { 3, 4, 6, 7 },
  { 4, 5, 7, 8 }
};

item idProperty( vector<JType> adj )
{
  item r;
  // Header
  r.header = 0;
  if( adj[3] == STRING ) r.header++;
  if( adj[4] == STRING ) r.header += 2;
  if( adj[5] == STRING ) r.header++;
  // Edge
  for( size_t it = 0; it < 4; it++ )
  {
    r.edge[it] = 0;
    if( adj[edges[it][0]] != NULLVALUE ) r.edge[it]++;
    if( adj[edges[it][1]] != NULLVALUE ) r.edge[it]++;
    if( adj[edges[it][2]] != NULLVALUE ) r.edge[it]++;
    if( adj[edges[it][3]] != NULLVALUE ) r.edge[it]++;
    if( adj[edges[it][4]] != NULLVALUE ) r.edge[it]++;
    if( adj[edges[it][5]] != NULLVALUE ) r.edge[it]++;
  }
  // Corner
  for( size_t it = 0; it < 4; it++ )
  {
    r.corner[it] = 0;
    if( adj[corners[it][0]] != NULLVALUE ) r.corner[it]++;
    if( adj[corners[it][1]] != NULLVALUE ) r.corner[it]++;
    if( adj[corners[it][2]] != NULLVALUE ) r.corner[it]++;
    if( adj[corners[it][3]] != NULLVALUE ) r.corner[it]++;
  }
  // Tuple
  r.tuple = 0;
  for( size_t it = 0; it < 9; it++ ) if( adj[it] != NULLVALUE ) r.tuple++;
  // Vertical bridge
  r.vbridge[0] = 0; r.vbridge[1] = 0; r.vbridge[2] = 0;
  if( adj[6] != NULLVALUE )
  {
    r.vbridge[0]++;
    r.vbridge[1]++;
  }
  if( adj[7] != NULLVALUE )
  {
    r.vbridge[0]++;
    r.vbridge[1]++;
    r.vbridge[2]++;
  }
  if( adj[8] != NULLVALUE )
  {
    r.vbridge[1]++;
    r.vbridge[2]++;
  }
  // Horizontal bridge
  r.hbridge = false;
  if( adj[3] != NULLVALUE && adj[5] != NULLVALUE &&
      ( ( adj[0] != NULLVALUE && adj[2] != NULLVALUE ) ||
        ( adj[6] != NULLVALUE && adj[8] != NULLVALUE ) ) )
  {
    r.hbridge = true;
  }
  // End
  return r;
}

vector< vector<item> > propertyGrid( sheet sh )
{
  size_t rows = sh.rows();
  size_t cols = sh.cols();
  vector< vector<item> > properties;
  for( size_t row = 0; row < rows; row++ )
  {
    vector<item> propertyrow;
    for( size_t col = 0; col < cols; col++ )
    {
      vector<JType> adj;
      for( int x = row-1; x <= (int)row+1; x++ )
      {
        for( int y = col-1; y <= (int)col+1; y++ )
        {
          if( x<0 || x>(int)rows-1 || y<0 || y>(int)cols-1 ) adj.push_back( NULLVALUE );
          else adj.push_back( sh[x][y].getType() );
        }
      }
      propertyrow.push_back( idProperty( adj ) );
    }
    properties.push_back( propertyrow );
  }
  return properties;
}

int evalVBridge( const vector< vector<item> > &pgrid, size_t crow, size_t start,
    size_t end )
{
  int midN = 2;
  int edgeN = 1;
  int score = 0;
  score += (int)pgrid[crow][start].vbridge[2] - edgeN;
  score += (int)pgrid[crow][end].vbridge[0] - edgeN;
  for( size_t it = start + 1; it < end; it++ )
  {
    score += (int)pgrid[crow][it].vbridge[1] - midN;
  }
  return score;
}

int evalTopRow( const vector< vector<item> > &pgrid, size_t crow, size_t start,
    size_t end )
{
  int edgeN = 4;
  int cornerN = 3;
  int score = 0;
  score += (int)pgrid[crow][start].corner[3] - cornerN;
  score += (int)pgrid[crow][end].corner[2] - cornerN;
  for( size_t it = start + 1; it < end; it++ )
  {
    score += (int)pgrid[crow][it].edge[1] - edgeN;
  }
  return score;
}

trow evalRow( const vector< vector<item> > &pgrid, size_t crow, size_t start,
    size_t end )
{
  int tupleN = 6;
  int edgeN = 4;
  int cornerN = 3;
  trow thisrow;
  thisrow.mid = 0;
  thisrow.bot = 0;
  thisrow.mid += (int)pgrid[crow][start].edge[3] - edgeN;
  thisrow.mid += (int)pgrid[crow][end].edge[2] - edgeN;
  thisrow.bot += (int)pgrid[crow][start].corner[1] - cornerN;
  thisrow.bot += (int)pgrid[crow][end].corner[0] - cornerN;
  for( size_t it = start + 1; it < end; it++ )
  {
    thisrow.mid += (int)pgrid[crow][it].tuple - tupleN;
    thisrow.bot += (int)pgrid[crow][it].edge[0] - edgeN;
  }
  return thisrow;
}

vector<tableRating> evalTableBody( const vector< vector<item> > &pgrid, 
    vector< vector<bool> > &allowed, size_t x1, size_t x2, size_t y )
{
  size_t crow = y+1;
  bool remaining = true;
  unsigned population = ( ((x2-x1)*(x2-x1)) + (x2-x1) )/2;
  tableRating nothingness;
  nothingness.score = 0;
  nothingness.depth = 0;
  vector<tableRating> scores( population, nothingness );
  while( crow < pgrid.size() && remaining )
  {
    remaining = false;
    size_t selection = 0;
    for( size_t start = x1; start < x2; start++ )
    {
      for( size_t end = start+1; end <= x2; end++ )
      {
        if( allowed[start-x1][end-start-1] )
        {
          trow thisrow = evalRow( pgrid, crow, start, end );
          if( crow == y+1 )
          {
            int bridgeScore = evalVBridge( pgrid, crow, start, end );
            if( bridgeScore > thisrow.mid ) thisrow.mid = bridgeScore;
          }
          if( crow == y+2 )
          {
            int topScore = evalTopRow( pgrid, crow, start, end );
            if( topScore > thisrow.mid ) thisrow.mid = topScore;
          }
          if( thisrow.mid > 0 || thisrow.bot > 0 ) 
          {
            if( thisrow.mid >= thisrow.bot )
            {
              remaining = true;
              scores[selection].score += thisrow.mid;
              scores[selection].depth++;
            }
            else
            {
              allowed[start-x1][end-start-1] = false;
              scores[selection].score += thisrow.bot;
              scores[selection].depth++;
            }
          }
          else allowed[start-x1][end-start-1] = false;
        }
        selection++;
      }
    }
    crow++;
  }
  return scores;
}


// Only call when tbl.x1, tbl.x2, and tbl.y1 have been initialized
bool idTable( const vector< vector<item> > &pgrid, size_t x1, size_t x2,
    size_t y, table &result )
{
  // Filter out headers at the bottom of the sheet
  if( pgrid.size() <= y+1 ) return false;
  // A diagonal grid representing each possible combination of x1 and x2 values
  vector< vector<bool> > allowed;
  for( size_t start = x1; start < x2; start++ )
  {
    vector<bool> endpoints;
    for( size_t end = start+1; end <= x2; end++ )
    {
      endpoints.push_back( true );
    }
    allowed.push_back( endpoints );
  }
  // Loops through each row, determining all the ways it may be valid. Ends
  // either at the end of the grid or when the last row has no more valid forms.
  vector<tableRating> scores = evalTableBody( pgrid, allowed, x1,
      x2, y );
  int max = -1;
  unsigned current = 0;
  for( size_t start = x1; start < x2; start++ )
  {
    for( size_t end = start+1; end <= x2; end++ )
    {
      //  end << "," << scores[current].depth << "] : " << scores[current].score << '\n';
      if( scores[current].score > max )
      {
        result.x1 = start;
        result.x2 = end;
        result.y1 = y;
        result.y2 = y + scores[current].depth;
        result.score = scores[current].score;
        max = scores[current].score;
      }
      current++;
    }
  }
  if( result.y1 != result.y2 ) return true; 
  return false;
}

bool containedIn( table a, table b )
{
  if( a.x1 >= b.x1 && a.x2 <= b.x2 && a.y1 >= b.y1 && a.y2 <= b.y2 ) return true;
  return false;
}

vector<table> scanTables( const vector< vector<item> > &pgrid )
{
  vector<table> result;
  size_t rows = pgrid.size();
  size_t cols = pgrid[0].size();
  for( size_t row = 0; row < rows; row++ )
  {
    table tbl;
    unsigned heads = 0;
    for( size_t col = 0; col < cols; col++ )
    {
      if( pgrid[row][col].header >= 4 )
      {
        // This shouldn't theoretically be possible at all given a valid grid.
        if( heads == 0 )
        {
          tbl.x1 = col;
          tbl.y1 = row;
        }
        heads++;
      }
      else
      {
        if( heads > 0 )
        {
          tbl.x2 = col;
          tbl.y2 = tbl.y1;
          bool contained = false;
          for( vector<table>::iterator it = result.begin();
              it != result.end(); it++ )
          {
            if( containedIn( tbl, *it ) ) contained = true;
          }
          if( !contained ) if( idTable( pgrid, tbl.x1, tbl.x2, tbl.y1, tbl ) )
          {
            result.push_back( tbl );
          }
          heads = 0;
        }
        else if( pgrid[row][col].header == 3 )
        {
          tbl.x1 = col;
          tbl.y1 = row;
          heads++;
        }
        else heads = 0;
      }
    }
  }
  return result;
}

void printProp( vector< vector<item> > pg )
{
  for( size_t x = 0; x < pg.size(); x++ )
  {
    for( size_t y = 0; y < pg[x].size(); y++ )
    {
      cout << "[" << x << "," << y << "]: ";
      cout << "H: " << pg[x][y].header << ", ";
      cout << "E: " << pg[x][y].edge[0] << "," << pg[x][y].edge[1] << "," <<
        pg[x][y].edge[2] << "," << pg[x][y].edge[3] << ", ";
      cout << "C: " << pg[x][y].corner[0] << "," << pg[x][y].corner[1] << "," <<
        pg[x][y].corner[2] << "," << pg[x][y].corner[3] << ", ";
      cout << "T: " << pg[x][y].tuple << ", ";
      cout << "V: " << pg[x][y].vbridge[0] << "," << pg[x][y].vbridge[1] <<
        "," << pg[x][y].vbridge[2];
      cout << '\n';
    }
  }
}

unsigned clashDegree( table a, table b )
{
  if( a.x1 > b.x2 || a.x2 < b.x1 ) return 0;
  else
  {
    if( a.y1 > b.y2 || a.y2 < b.y1 ) return 0;
    else return 1;
  }
}

void removeClashes( vector<table> &input )
{
  size_t current = 0;
  while( current < input.size() )
  {
    size_t next = current + 1;
    while( next < input.size() )
    {
      if( clashDegree( input[current], input[next] ) > 0 )
      {
        if( input[next].score > input[current].score )
        {
          input.erase( input.begin() + current );
          current = 0;
          next = current + 1;
        }
        else input.erase( input.begin() + next );
      }
      else next++;
    }
    current++;
  }
}

vector<table> getSheetTables( sheet sh )
{
  vector< vector<item> > pgrid = propertyGrid( sh );
  vector<table> result = scanTables( pgrid );
  removeClashes( result );
  return result;
}

