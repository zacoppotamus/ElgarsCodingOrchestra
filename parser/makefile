CXX = g++
CPPFLAGS = -std=c++0x -O3 -g -Wall -Iunzip
ZPATH = unzip
PARSE_SRCS = SadParser.cpp processTables.cpp readFile.cpp JSONWriter.cpp
PARSE_OBJS = $(PARSE_SRCS:%.cpp=%.o)
ABS_UNZ_OBJS = miniunz.o unzip.o ioapi.o libz.a
UNZ_OBJS = $(ABS_UNZ_OBJS:%=$(ZPATH)/%)

default: sadparser

sadparser: $(PARSE_OBJS) $(UNZ_OBJS)
	$(CXX) $(CPPFLAGS) -o $@ $(PARSE_OBJS) $(UNZ_OBJS)

$(PARSE_OBJS): %.o: %.cpp
	$(CXX) $(CPPFLAGS) -c $<

$(UNZ_OBJS):
	cd unzip && $(MAKE)	

clean:
	$(RM) sadparser *.o *~
	cd unzip && $(MAKE) clean
