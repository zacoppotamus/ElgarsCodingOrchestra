import numpy                             # To calculate correlation
import json                              # To convert to JSON
import sys                               # To get arguments
import warnings                          # To suppress numpy warnings
from pymongo import MongoClient          # To communicate with MongoDB
warnings.simplefilter('ignore', numpy.RankWarning)

def graphEquation(xPoints, yPoints, degree):
    x = numpy.array(xPoints)
    y = numpy.array(yPoints)
    xPoly = numpy.polyfit(x, y, degree)
    return numpy.poly1d(xPoly)

def queryEquation(x1Query, x2Query, x1Points, x2Points, yPoints, degree, x1Weight=1, x2Weight=1):
    if(x1Weight < 0 or x2Weight < 0):
        raise("Weights must be greater than 0")

    x1Eq = graphEquation(x1Points, yPoints, degree) * x1Weight
    x2Eq = graphEquation(x2Points, yPoints, degree) * x2Weight

    x1 = numpy.asscalar(x1Eq(float(x1Query)))
    x2 = numpy.asscalar(x2Eq(float(x2Query)))

    return (x1 + x2)/(x1Weight + x2Weight)

def query(databaseName, collectionName, x1Query, x2Query, x1Name, x2Name, yName, degree, x1Weight=1, x2Weight=1):
    client     = MongoClient('spe.sneeza.me', 27017)
    db         = client[databaseName]
    collection = db[collectionName]

    x1Points = [p[x1Name] for p in collection.find()]
    x2Points = [p[x2Name] for p in collection.find()]
    yPoints  = [p[yName ] for p in collection.find()]

    equation = queryEquation(x1Query, x2Query, x1Points, x2Points, yPoints, degree, x1Weight, x2Weight)
    print json.dumps(equation)
    return json.dumps(equation)

def main():
    if(len(sys.argv) == 11):
        query(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5], sys.argv[6], sys.argv[7], sys.argv[8], sys.argv[9], sys.argv[10])
    elif(len(sys.argv) == 9):
        query(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5], sys.argv[6], sys.argv[7], sys.argv[8])
    else:
        print "Usage: correlation.py databaseName collectionName x1 x2 x1FieldName x2FieldName yFieldName degree x1Weight x2Weight"
        print "       OR"
        print "       correlation.py databaseName collectionName x1 x2 x1FieldName x2FieldName yFieldName degree"



if __name__ == "__main__":
    main()
