/**
 * Convert 8 char hexadecimal color into RGBA color
 * @param 8 characters of hexadecimal color value. Last two character stands for alpha 0-255
 * @returns RGBA representation string
 */

window.N2Color = {
    hex2rgba: function (str) {
        var num = parseInt(str, 16); // Convert to a number
        return [num >> 24 & 255, num >> 16 & 255, num >> 8 & 255, (num & 255) / 255];
    },
    hex2rgbaCSS: function (str) {
        return 'RGBA(' + N2Color.hex2rgba(str).join(',') + ')';
    },
    hexdec: function (hex_string) {
        hex_string = (hex_string + '').replace(/[^a-f0-9]/gi, '');
        return parseInt(hex_string, 16);
    },

    hex2alpha: function (str) {
        var num = parseInt(str, 16); // Convert to a number
        return ((num & 255) / 255).toFixed(3);
    },
    colorizeSVG: function (str, color) {
        var parts = str.split('base64,');
        if (parts.length == 1) {
            return str;
        }
        parts[1] = Base64.encode(Base64.decode(parts[1]).replace('fill="#FFF"', 'fill="#' + color.substr(0, 6) + '"').replace('opacity="1"', 'opacity="' + N2Color.hex2alpha(color) + '"'));
        return parts.join('base64,');
    }
};