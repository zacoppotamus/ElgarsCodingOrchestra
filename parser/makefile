CC = g++
CFLAGS = -std=c++11 -g -Wall

default: Sadparser

Sadparser: readFile.o processTables.o JSONWriter.o SadParser.o
	$(CC) $(CFLAGS) -o sadparser SadParser.o processTables.o readFile.o JSONWriter.o

readFile.o: readFile.cpp readFile.h
	$(CC) $(CFLAGS) -c readFile.cpp

processTables.o: processTables.cpp readFile.h processTables.h JSONWriter.h
	$(CC) $(CFLAGS) -c processTables.cpp

JSONWriter.o: JSONWriter.cpp JSONWriter.h
	$(CC) $(CFLAGS) -c JSONWriter.cpp

SadParser.o: SadParser.cpp readFile.h processTables.h JSONWriter.h
	$(CC) $(CFLAGS) -c SadParser.cpp

clean:
	$(RM) sadparser *.o *~
