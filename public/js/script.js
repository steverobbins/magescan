var MGA;
(function($) {
    MGA = {
        scan: function(url) {
            this.url = encodeURIComponent(url);
            this.get('magentoinfo');
            this.get('modules');
            this.get('catalog');
            this.get('sitemap');
            this.get('servertech');
            this.get('unreachablepath');
        },
        get: function(code) {
            var that = this;
            $.get('ajax.php?code=' + code + '&url=' + this.url, function(response) {
                $('#' + code ).find('.response').html(that.render(jQuery.parseJSON(response)));
            });
        },
        render: function(data) {
            var html = '<table class="table">',
                noHead = true;
            if (data.head) {
                html += '<thead><tr>';
                for (var i = 0; i < data.head.length; ++i) {
                    var cell = data.head[i];
                    html += '<th>' + cell + '</th>';
                }
                html += '</thead></tr>';
                noHead = false;
            }
            html += '<tbody>';
            for (var i = 0; i < data.body.length; ++i) {
                var row = data.body[i];
                html += '<tr>';
                for (var j = 0; j < row.length; ++j) {
                    var cell = row[j];
                    if (noHead && j == 0) {
                        html += '<th>' + cell + '</th>';
                    } else {
                        html += '<td>' + cell + '</td>';
                    }
                }
                html += '</tr>';
            }
            html += '</tbody></table>';
            return html;
        }
    };
})(jQuery);
