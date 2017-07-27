//function cmyk2rgb(c, m, y, k) {
// R = 255 × (1-C) × (1-K)
// G = 255 × (1-M) × (1-K)
// B = 255 × (1-Y) × (1-K)
//}

//function rgb2cmyk(r, g, b) {
// R' = R/255
// G' = G/255
// B' = B/255
// K = 1-max(R', G', B')
// C = (1-R'-K) / (1-K)
// M = (1-G'-K) / (1-K)
// Y = (1-B'-K) / (1-K)
//}

var cmyk2rgb = function(c, m, y, k, normalized) {
    c = (c / 100);
    m = (m / 100);
    y = (y / 100);
    k = (k / 100);

    c = c * (1 - k) + k;
    m = m * (1 - k) + k;
    y = y * (1 - k) + k;

    var r = 1 - c;
    var g = 1 - m;
    var b = 1 - y;

    if (!normalized) {
        r = Math.round(255 * r);
        g = Math.round(255 * g);
        b = Math.round(255 * b);
    }

    return {
        r : r,
        g : g,
        b : b
    }
}

var rgb2cmyk = function(r, g, b, normalized) {
    var c = 1 - (r / 255);
    var m = 1 - (g / 255);
    var y = 1 - (b / 255);
    var k = Math.min(c, Math.min(m, y));

    c = (c - k) / (1 - k);
    m = (m - k) / (1 - k);
    y = (y - k) / (1 - k);

    if (!normalized) {
        c = Math.round(c * 10000) / 100;
        m = Math.round(m * 10000) / 100;
        y = Math.round(y * 10000) / 100;
        k = Math.round(k * 10000) / 100;
    }

    c = isNaN(c) ? 0 : c;
    m = isNaN(m) ? 0 : m;
    y = isNaN(y) ? 0 : y;
    k = isNaN(k) ? 0 : k;

    return {
        c : c,
        m : m,
        y : y,
        k : k
    }
}

// 1个rgb,转成一个cmyk,提取出c,再转成一个rgb,即为c层
function rgb2c(r, g, b, a) {
    var cmyk = rgb2cmyk(r, g, b, 0);
    var rgb = cmyk2rgb(cmyk.c, 0, 0, 0, 0);
    rgb.a = a;
    return rgb;
}

// m
function rgb2m(r, g, b, a) {
    var cmyk = rgb2cmyk(r, g, b, 0);
    var rgb = cmyk2rgb(0, cmyk.c, 0, 0, 0);
    rgb.a = a;
    return rgb;
}

// y
function rgb2y(r, g, b, a) {
    var cmyk = rgb2cmyk(r, g, b, 0);
    var rgb = cmyk2rgb(0, 0, cmyk.c, 0, 0);
    rgb.a = a;
    return rgb;
}

// k
function rgb2k(r, g, b, a) {
    var cmyk = rgb2cmyk(r, g, b, 0);
    var rgb = cmyk2rgb(0, 0, 0, cmyk.c, 0);
    rgb.a = a;
    return rgb;
}

function rgbLayer(r, g, b, a, layer){
    if(layer == "c"){
        return rgb2c(r, g, b, a);
    } else if (layer == "m"){
        return rgb2m(r, g, b, a);
    } else if (layer == "y"){
        return rgb2y(r, g, b, a);
    } else if (layer == "k"){
        return rgb2k(r, g, b, a);
    }
}