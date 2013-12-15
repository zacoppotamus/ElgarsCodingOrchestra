import numpy

def graphEquation(xPoints1, xPoints2, yPoints, degree, x1Weight=1, x2Weight=1):
    if(x1Weight > 1 or x1Weight < 0 or x2Weight > 1 or x2Weight < 0):
        raise("Weight must be between 0 and 1")

    x1 = numpy.array(xPoints1)
    x2 = numpy.array(xPoints2)
    y  = numpy.array(yPoints )

    x1Poly = numpy.poly1d(numpy.polyfit(x1, y, degree)) * x1Weight
    x2Poly = numpy.poly1d(numpy.polyfit(x2, y, degree)) * x2Weight
    return (x1Poly + x2Poly)/(x1Weight + x2Weight)


def main():
    time   = [5,4,3,2,1]
    budget = [5,4,3,3,3]
    crime  = [5,4,3,2,1]

    print graphEquation(time, budget, crime, 2)


if __name__ == "__main__":
    main()
