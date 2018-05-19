function showMultiTree(tier_selected, $test_data){
                $("#mytreeselect").empty();
                $("#firstpane div").empty();
                $("#firstpane div").remove();
                // $test_data =$.parseJSON('{$page_1}');
                console.log($test_data);
                console.log(11111111111111);

                if (tier_selected == "账户") {
                    $.each($test_data, function(index, val) {
                        $.each(val, function(index1, val1) {
                            $.each(val1, function(index2, val2) {
                                $("#mytreeselect").append('<option value=' + val2.id + ' data-section="' + index+'/'+index1 + '">' + val2.user_login + '</option>');
                            });
                        });
                    });
                }

                if (tier_selected == "计划") {
                    $.each($test_data, function(index, val) {
                        $.each(val, function(index1, val1) {
                            $("#mytreeselect ").append('<option value=' + val1.campaignId + ' data-section="' + index + '">' + val1.campaignName + '</option>');
                        });
                    });
                }
                if (tier_selected == "单元") {
                    $.each($test_data, function(index, val) {
                        $.each(val, function(index1, val1) {
                            $.each(val1, function(index2, val2) {
                                 $("#mytreeselect ").append('<option value=' + val2.adgroupId + ' data-section="' + index+'/'+index1 + '">' + val2.adgroupName + '</option>');
                            });
                        });
                    });
                }
                if (tier_selected=="关键词") {
                    $.each($test_data, function(index, val) {
                        $.each(val, function(index1, val1) {
                            $.each(val1, function(index2, val2) {
                                 $.each(val2, function(index3, val3) {
                                    $("#mytreeselect ").append('<option value=' + val3.keywordId + ' data-section="' + index+'/'+index1 +'/'+index2+ '">' + val3.keyword + '</option>');
                                 });   
                            });
                        });
                    });  
                }
                if (tier_selected=="创意") {
                    $.each($test_data, function(index, val) {
                        $.each(val, function(index1, val1) {
                            $.each(val1, function(index2, val2) {
                                 $.each(val2, function(index3, val3) {
                                     $("#mytreeselect ").append('<option value=' + val3.creativeId + ' data-section="' + index+'/'+index1 +'/'+index2+ '">' + val3.title + '</option>');
                                 });  
                            });
                        });
                    });  
                }

                option_tree1_0 = {
                    allowBatchSelection: true,
                    enableSelectAll: true,
                    searchable: true,
                    sortable: true,
                    startCollapsed: true,
                    onChange: function(text, value, initialIndex, section) {
                        $temp = text;
                        console.log($temp);
                    }
                };
                $tree1 = $("#mytreeselect").treeMultiselect(option_tree1_0);

            }