import numpy                             # To calculate correlation
import json                              # To convert to JSON
import sys                               # To get arguments
import warnings                          # To suppress numpy warnings
from pymongo import MongoClient          # To communicate with MongoDB
warnings.simplefilter('ignore', numpy.RankWarning)

def graphEquation(xPoints, yPoints, degree):
    x = numpy.array(xPoints)
    y = numpy.array(yPoints)
    poly = numpy.polyfit(x, y, degree)
    return poly.tolist()

def query(databaseName, collectionName, xName, yName, degree):
    client     = MongoClient('spe.sneeza.me', 27017)
    db         = client[databaseName]
    collection = db[collectionName]

    xPoints = [p[xName] for p in collection.find()]
    yPoints = [p[yName] for p in collection.find()]

    equation = graphEquation(xPoints, yPoints, degree)
    print json.dumps(equation)
    return json.dumps(equation)

def main():
    if(len(sys.argv) == 6):
        return query(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5])
    else:
        print "Usage: correlation.py databaseName collectionName xFieldName yFieldName degree"



if __name__ == "__main__":
    main()
