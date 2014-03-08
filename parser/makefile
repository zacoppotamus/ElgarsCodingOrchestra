CC = g++
CPPFLAGS = -std=c++11 -g -Wall
PARSE_SRCS = SadParser.cpp processTables.cpp readFile.cpp JSONWriter.cpp
PARSE_OBJS = $(PARSE_SRCS:%.cpp=%.o)
LINK_DLLS = miniunz.dll

default: sadparser

sadparser: $(PARSE_OBJS)
	$(CC) $(CPPFLAGS) -o $@ $(PARSE_OBJS) $(LINK_DLLS)

$(PARSE_OBJS): %.o: %.cpp
	$(CC) $(CPPFLAGS) -c $<

clean:
	$(RM) sadparser *.o *~
