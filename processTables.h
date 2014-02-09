#ifndef TABLEPROCESSOR
#define TABLEPROCESSOR

#include "JSONWriter.h"
#include "readFile.h"
#include <vector>

vector< vector<JSONObject> > processData( vector< vector<sheetNode> > spreadsheet );

#endif
