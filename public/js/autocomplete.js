let path = window.location.origin + '/point-app/public/api/customers?q={query}'
$('.ui.search')
    .search({
        apiSettings: {
            onResponse : function(data) {
                var response = {
                    results : []
                }

                $.each(data, function(index, name) {
                    response.results.push({title : name})
                })

                return response
            },
            url: path
        },
        selectFirstResult: true,
    });