var MageScan;
(function($) {
    $(document).ready(function() {
        $('.panel .label-info').each(function() {
            var info = $(this).closest('.panel').find('.alert-info');
            $(this).hover(function() {
                info.slideDown(100);
            }, function() {
                info.slideUp(100);
            });
        });
    });
    MageScan = {
        scan: function(url) {
            this.url = encodeURIComponent(url);
            this.get('magentoinfo');
            this.get('modules', this.processModules);
            this.get('patch');
            this.get('catalog');
            this.get('sitemap');
            this.get('servertech');
            this.get('unreachablepath', this.processUnreachablePath);
        },
        processModules: function (that, code, response) {
            $('#' + code ).find('.response').html($('#' + code ).find('.response').html() + that.render({"head":["Module","Installed"],"body":[]}));
            var count = response.length,
                done  = 0,
                shown = 0;
            for (var i = 0; i < response.length; ++i) {
                var url = response[i];
                $.get('ajax.php?code=modulessingle&path=' + url + '&url=' + that.url, function(response) {
                    console.log(response);
                    if (response) {
                        var response = jQuery.parseJSON(response);
                        shown++;
                        $('#' + code ).find('.response tbody').append('<tr><td>' + response[0] + '</td><td class="pass">Yes</td></tr>');
                    }
                    if (++done == count) {
                        $('#' + code ).find('.loader').remove();
                        if (shown == 0) {
                            $('#' + code ).find('.response').html('No detectable modules were found');
                        }
                    }
                });
            }
        },
        processUnreachablePath: function (that, code, response) {
            $('#' + code ).find('.response').html($('#' + code ).find('.response').html() + that.render({"head":["Path","Response Code","Status"],"body":[]}));
            var count = response.length,
                done  = 0,
                shown = 0;
            for (var i = 0; i < response.length; ++i) {
                var url = response[i];
                $.get('ajax.php?code=unreachablepathsingle&path=' + url + '&url=' + that.url, function(response) {
                    if (response) {
                        shown++;
                        var response = jQuery.parseJSON(response);
                        $('#' + code ).find('.response tbody').append('<tr><td>' + response[0] + '</td><td>' + response[1] + '</td><td>' + response[2] + '</td></tr>');
                    }
                    if (++done == count) {
                        $('#' + code ).find('.loader').remove();
                        if (shown == 0) {
                            $('#' + code ).find('.response').html('No sensitive URLs were found');
                        }
                    }
                });
            }
        },
        get: function(code, callback) {
            var that = this;
            $.get('ajax.php?code=' + code + '&url=' + this.url, function(response) {
                if (typeof(callback) == "function") {
                    callback(that, code, jQuery.parseJSON(response))
                } else {
                    $('#' + code ).find('.response').html(that.render(jQuery.parseJSON(response)));
                }
            }).error(function(a) {
                $('#' + code ).find('.response').html('<div class="alert alert-danger">' + a.statusText + '</div>');
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
