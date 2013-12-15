import numpy      # To calculate correlation
import json       # To convert to JSON
import sys        # To get arguments
import warnings   # To suppress numpy warnings

from pymongo import MongoClient
warnings.simplefilter('ignore', numpy.RankWarning)

def graphEquation(x1Points, x2Points, yPoints, degree, x1Weight=1, x2Weight=1):
    if(x1Weight > 1 or x1Weight < 0 or x2Weight > 1 or x2Weight < 0):
        raise("Weight must be between 0 and 1")

    x1 = numpy.array(x1Points) * x1Weight
    x2 = numpy.array(x2Points) * x2Weight
    y  = numpy.array(yPoints )

    x1Poly = (numpy.polyfit(x1, y, degree))
    x2Poly = (numpy.polyfit(x2, y, degree))
    return (x1Poly + x2Poly)/(x1Weight + x2Weight)


def query(databaseName, collectionName, x1Name, x2Name, yName, degree, x1Weight=1, x2Weight=1):
    client     = MongoClient('spe.sneeza.me', 27017)
    db         = client[databaseName]
    collection = db[collectionName]

    x1Points = [p[x1Name] for p in collection.find()]
    x2Points = [p[x2Name] for p in collection.find()]
    yPoints  = [p[yName ] for p in collection.find()]

    equation = graphEquation(x1Points, x2Points, yPoints, degree, x1Weight, x2Weight).tolist()
    print json.dumps(equation)
    return json.dumps(equation)

def main():
    #print sys.argv
    query(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5], sys.argv[6])


if __name__ == "__main__":
    main()
