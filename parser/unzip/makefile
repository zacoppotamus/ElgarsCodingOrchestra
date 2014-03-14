CC=cc
CFLAGS=-O3

UNZ_OBJS = miniunz.o unzip.o ioapi.o libz.a

.c.o:
	$(CC) -c $(CFLAGS) $*.c

all: $(UNZ_OBJS)

%.o: %.c
	$(CC) $(CFLAGS) -c $<

clean:
	$(RM) -f *.o *~ all
