/**
 * Checks if the array of points contains a specific point.
 * @param array array<array>   An array of array which is a point.
 * @param point array<integer> An array containing two elements [x, y].
 * @return true if array contains the point.
 *         false if:
 *             array is null or undefined
 *             point is null or undefined
 *             array does not contain the point
 */
function arrayHasPoint(array, point) {
    if(!array || !point)
        return false;

    for(var i = 0; i < array.length; i++)
        if(array[i] && array[i][0] == point[0] && array[i][1] == point[1])
            return true;
    return false;
}

/**
 * Shuffles an array in place.
 *
 * @param array The array to be shuffled.
 * @returns the array itself.
 */
function shuffle(array) {
    var currentIndex = array.length, temporaryValue, randomIndex;

    // While there remain elements to shuffle...
    while (0 !== currentIndex) {

      // Pick a remaining element...
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex -= 1;

      // And swap it with the current element.
      temporaryValue = array[currentIndex];
      array[currentIndex] = array[randomIndex];
      array[randomIndex] = temporaryValue;
    }

    return array;
}

/**
 * Trim null from the end of array, in place.
 *
 * @param array The array to be trimmed.
 * @returns the array itself.
 */
function trimArray(array) {
    var p = 0;
    for(var i = 0; i < array.length; i++) {
        if(array[i] == null) {
            p = i;
            break;
        }
    }
    var res = array.splice(0, p);
    return res;
}

/**
 * No operation
 */
function nop() {}

var PointCollection = {
    indexOf: function(pointArray, point) {
        for(var i = 0; i < pointArray.length; i++)
            if(pointArray[i] && pointArray[i][0] == point[0] && pointArray[i][0] == point[0] && pointArray[i][1] == point[1])
                return i;
        return -1;
    }
};

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}