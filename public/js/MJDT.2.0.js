/**
 * @author	xu.sun
 * @email	mj_0203@hotmail.com
 * @desc	MJ dataTable	table列表展示【异步获取数据】
 * 			详细请查看wiki
 * @description		可供调用方法	[enableFirstControl=>强制调用第一列控制, controStyle=>样式, 
 * 									drawSearch=>搜索, generate=>default生成list, init=>初始化参数
 * 									get_first_control_data
 * 								]
 * @example	
 * 		MJDT.init(options).generate(your_server_api_path);
 * 			OR
 * 		MJDT.init(options);
 * 			OR
 *		MJDT.generate(your_server_api_path);
 *  
 *  search
 *		MJDT.drawSearch('.searchForm');
 *			OR
 *		MJDT.drawSearch();
 */
var MJDT = {
		options : {
			'first_control_columns' : true,		//第一列选择控制列
			'repeat_page' 			: false,	//是否重复翻页
			'onJumpPage' 			: true,		//是否启用跳页
			'title'					: false,	//是否启用重置title   title=my custom title
			'showPageList' 			: true,		//是否启用pageList
			'overflowX'				: true,		//是否允许横向滚动
			
			'controls'				: [{'ysort':false, 'search':true}, {'ysort':false, 'search':false}],		//列控制布局命令  ysort 是否启用排序, search 是否支持搜索列
			'orderColumn'			: {},		//排序
			
			'page_default_list' 	: 10,		//默认首页显示页码
			'page_gap' 				: 5,		//间隔规则
			'page' 					: 1,		//初始化开始页
			'page_size' 			: 10,		//初始化每页显示条数
			'page_select'			: [10,20,40,60,80,100],	//页码设置
			
			'server_url' 			: '/',		//server	Api
			'paramData' 			: {},		//server 	data
			'method' 				: 'post',	//server	method
			
			'next_btn_name' 		: '下一页',
			'prev_btn_name' 		: '上一页',
			'home_btn_name' 		: '首页',
			'end_btn_name' 			: '末页',
			
			'columns' 				: {},		//字段列表
			'tableNode' 			: 'table',	//table 节点class	默认table
			'searchNode'			: 'form',	//搜索表单节点	default	form
			
			'globalStyle' 			: {},
			
			'onReturnSubmit' 		: true,		//是否启用回车搜索
			'maskTable' 			: true		//生成table时是否遮罩
		},
		//初始化参数
		init : function(options){
			if(options){
				for(var k in options){
					this.options[k] = options[k];
				}
			}
			if(this.options.title!=false) $('title').text(this.options.title);
			
			this.clone_options();
			
			this.default_onsubmit();
			
			return this;
		},
		//生成data list
		generate : function(url, callback){
			this.mask_table('open');
			
			if(url && typeof url==='function'){
				callback = url;
				url = null;
			}
				
			if(!url)
				url = this.options.server_url;
			else
				this.options.server_url = url;
			
			this.options.paramData.page = this.options.page;
			this.options.paramData.page_size = this.options.page_size;
			
			var g_this = this;
			
			this.get_first_control_data();//组织控制列数据
			
			$(this.options.tableNode).prop('action', this.options.server_url);//记录table	url
			
			$.ajax({
                type : this.options.method,
                url : url,
                data : this.options.paramData,
                dataType : typeof dataType !=='undefined' ? dataType : 'json',
                success : function(result){
                	result = result.data;
                	if(typeof result.data!=='undefined'){
                		if(g_this.isEmpty(result.data)){
                			console.warn('data is empty!');
                			g_this.mask_table('close');
                		}
                	}else{
                		console.warn('data is not found!');
                		g_this.mask_table('close');
                	}
                	g_this.eachList(result, function(){
            			if(typeof callback==='function')
            				callback(result);
            		});
                }
			});
			return this;
		},
		isEmpty : function(ArrayOrObject){
			for(var k in ArrayOrObject){
				return false
			}
			return true;
		},
		//实现列命令控制
		controlsColumn : function(result, firstLineNode, callback){
			var orderColumn = result.orderColumn||[];
			var fields = this.options.columns||result.data[0];
			
			if(firstLineNode.length){
				var lineHeight = 20;
				firstLineNode.each(function(i, lineHTMLTableCellElement){
					$(lineHTMLTableCellElement).attr('field', MJDT.options.columns[i]);
					var field = $(lineHTMLTableCellElement).attr('field');
					if(!lineHeight)
						lineHeight 	= $(lineHTMLTableCellElement).height();

					$(lineHTMLTableCellElement).find('span').remove();//翻页重置排序图标
					if(typeof MJDT.options.controls[i]!='undefined'){
						var ysort = MJDT.options.controls[i].ysort;
						if(ysort===true){
							$(lineHTMLTableCellElement).css({'cursor':'pointer', 'vertical-align':'middle'});
							var sortStyleObj = {'width':'0','height':'0','vertical-align':'middle','display':'inline-block',
												'border-left':'5px solid transparent','border-right':'5px solid transparent'};
							
							var mjHeadSortUpHtml = "<span class='mjHeadSortUp' style='border-bottom:5px solid'></span>",
								mjHeadSortDownHtml = "<span class='mjHeadSortDown' style='border-top:5px solid'></span>";
							if(typeof orderColumn[field]!='undefined'){
								var server_ysort = orderColumn[field].toLowerCase();
								$(lineHTMLTableCellElement).attr('server_ysort', server_ysort);
								if(server_ysort=='asc'){
									$(lineHTMLTableCellElement).append(mjHeadSortUpHtml);
								}else{
									$(lineHTMLTableCellElement).append(mjHeadSortDownHtml);
								}
								sortStyleObj['margin-left'] = '3px';
							}else{
								$(lineHTMLTableCellElement).append(mjHeadSortUpHtml+mjHeadSortDownHtml);
								$(lineHTMLTableCellElement).attr('server_ysort', 'asc');
								$(lineHTMLTableCellElement).find('.mjHeadSortUp').css({'margin-top':'-5px', 'margin-right':'-5px', 'margin-left':'3px'});
								$(lineHTMLTableCellElement).find('.mjHeadSortDown').css({'margin-bottom':'-6px', 'margin-left':'-5px'});
							}
							$(lineHTMLTableCellElement).find('span.mjHeadSortUp').css(sortStyleObj);
							$(lineHTMLTableCellElement).find('span.mjHeadSortDown').css(sortStyleObj);
							
							MJDT.ysortEvent(lineHTMLTableCellElement, lineHeight);
						}
					}
				});
			}

			/* 输出完数据后可以干点儿啥... */
			//横向滚动
			if(this.options.overflowX===true){
				$(this.options.tableNode).parent().css({'overflow-x':'scroll'});
				var tableHead = $(this.options.tableNode +' tr').eq(0).find('th')||$(this.options.tableNode +' tr').eq(0).find('td');
				
				var tableWidth = 0,
					tableElement = $(this.options.tableNode),
					minWidth = tableElement.parent().width()||1000;
				
				tableHead.each(function(){
					var _style = $(this).attr('style');
					if(_style){
						_style = _style.replace(/\s/g, "").split('width:');
						if(typeof _style[1]!='undefined')
							tableWidth += parseInt(_style[1].split(';')[0]);
					}
				})
				$(this.options.tableNode).css({'min-width':minWidth+'px', 'max-width':'none', 'width':tableWidth+'px'});
			}
		},
		//点击排序事件
		ysortEvent : function(Element, lineHeight){
			$(Element).unbind('click');
			
			$(Element).click(function(){
				$('th[ysort]').removeAttr('ysort');
				$(Element).attr('ysort', $(Element).attr('server_ysort')=='asc' ? 'desc' : 'asc');
				MJDT.drawSearch();//调用搜索api
			});
			//some things...
		},
		//循环数据组织列表
		eachList : function(result, callback){
			var g_this = this;
			if(result){
				if($(this.options.tableNode + ' .dataListCommon').length){
					$(this.options.tableNode + ' .dataListCommon').remove();
				}
				
				if($('.'+this.pageListExtendsNode()))
					$('.'+this.pageListExtendsNode()).remove();
				
				var data = [];
				if(typeof result.data!=='undefined'){
					var data = result.data;
				}else{
					console.warn('eachList: data empty!');
				}
				
				var firstTrNode = $((this.options.tableNode) + ' tr').eq(0),
					firstLineNode = null;
				if(firstTrNode.children('th').length){
					firstLineNode = firstTrNode.children('th')
				}else{
					firstLineNode = firstTrNode.children('td');
				}
				g_this.controlsColumn(result, firstLineNode);//控制行列
				
				if(this.isEmpty(data)){
					if(typeof callback==='function'){
						callback({'data' : result, 'msg' : 'data empty'});
						return;
					}
				}
				
				var column_length = firstLineNode.length;

				var list_html = '';//初始化

				var trNum = 0;
				for(var key in data){//行
					trNum++;

					list_html += '<tr class="dataListCommon list_tr_'+ trNum +'">';
					
					var tdData = data[key],
						tmp = []
					for(var tdkey in tdData){
						tmp.push(tdkey)
					}
					var _ck=0;
					for(var i=0;i<column_length;i++){
						var res = this.custom_columns(i, tmp[i], tdData, trNum);
						list_html +='<td class="list_td_'+ res.field +'_'+ trNum.toString()+(_ck+1) +'">' + res.key + '</td>';
						_ck++;
					}
					list_html +='</tr>'
					$((this.options.tableNode)).append(list_html);//逐条追加
					list_html = '';//复位

				}
				this.first_control_columns();//增加控制列
				
				this.after();//收尾事宜
				this.controStyle();//控制样式
				
				this.mask_table('close');
			}
			
			if(this.options.showPageList===true){
				this.pageList(result.pageData, function(){
					g_this.resetParamData();
					if(typeof callback==='function')
	    				callback();
				});
			}else{
				g_this.resetParamData();
				if(typeof callback==='function')
    				callback();
			}
		},
		resetParamData : function(){
			for(var tmpA in this.options.paramData){
				delete this.options.paramData[tmpA];
			}
		},
		//自定义columns
		custom_columns : function(countNum, tdkey, tdData, trNum){
			if(this.options.columns==null){
				var res = {'key' : tdData[tdkey], 'field' : tdkey};
			}else{
				if(typeof this.options.columns[countNum]==='string'){
					var field = this.options.columns[countNum];
					if(this.options.columns[countNum]==='auto_id')
						var res = trNum;
					
					if(this.options.columns[countNum].indexOf('|')>0){//或展示
						field = '';
						var orData = this.options.columns[countNum].split('|');	
						for(var o in orData){
							if(orData[o]=='auto_id'){
								var res = trNum;
								field = o;
								break;
							}
							if(typeof tdData[orData[o]]!=='undefined'){
								var res = tdData[orData[o]];
								field = orData[o];
								break;
							}
						}
					}else if(this.options.columns[countNum].indexOf('*')>=0 && this.options.columns[countNum].indexOf('(')>0){
							var res = '',
							field = 'Cfunction';
						
						var p = this.options.columns[countNum].replace(/\*/g, ''),
							p = p.split('('),
							func = p[0],
							data = p[1].replace(')', '');
						data = data.split('&');
						var pdata = new Array();
						if(data.length){
							for(var i in data){
								pdata[data[i]] = tdData[data[i]];
							}
						}
						if(typeof this[func]=='function')
							res = this[func](data.length ? pdata : tdData, data.length ? tdData : '');
						else
							console.warn('function ['+func+'] not found!');
					}else if(this.options.columns[countNum].indexOf('&')>0){//并集展示
						var andData = this.options.columns[countNum].split('&');
						var res = '', 
							field = '';

						for(var a in andData){
							if(typeof tdData[andData[a]]!=='undefined'){
								res += tdData[andData[a]];
								field.length ? field += '_' + andData[a] : field += andData[a];
							}else if(andData[a]=='auto_id'){
								res += (this.options.page-1) * this.options.page_size + trNum;
							}else{
								res += andData[a];
							}
						}
					}else if(this.options.columns[countNum].indexOf('>')>0){//按钮操作
						var res = this.options.columns[countNum],
							field = '';
					}
					var res = {'key' : typeof res!=='undefined' ? res : tdData[this.options.columns[countNum]], 'field' : field};
				}else{
					var res = {'key' : tdData[tdkey], 'field' : tdkey};
				}
			}
			return res;
		},
		pageListExtendsNode : function(){
			return 'pageList_extends_' + this.options.tableNode.replace(/\./g, '_').replace(/\#/g, '__');
		},
		formListExtendsNode : function(){
			return 'current_tableStr_' + this.options.searchNode.replace(/\./g, '_').replace(/\#/g, '__');
		},
		currentTableNode : function(){
			return this.options.tableNode.replace(/\./g, '_').replace(/\#/g, '__');
		},
		//页码设置
		pageSelect : function(){
			if(typeof this.options.page_select==='object'){
				var page_select_h = '<div class="pageListPageSelect" style="float:left">页码: <select style="width:60px">';
				for(var size in this.options.page_select){
					page_select_h += '<option value='+this.options.page_select[size]+'>'+this.options.page_select[size]+'</option>';
				}
				page_select_h += '</select></div>';


				$('.'+this.pageListExtendsNode()).prepend(page_select_h);

				$('.'+this.pageListExtendsNode() + ' .pageListPageSelect select option[value='+this.options.page_size+']').attr('selected', true);
				
				$('.'+this.pageListExtendsNode()+' .pageListPageSelect select').change(function(){
					var setPage = parseInt(this.value);
					if(MJDT.options.page==setPage)
						return false;

					MJDT.options.page_size = setPage;
					MJDT.drawSearch();
				});
			}
		},
		//翻页list
		pageList : function(pageData, callback){
			if(pageData){
				if(typeof pageData.total_page!=='undefined'){
					var pageList = '<div class="pageList '+this.pageListExtendsNode()+'" style="width:100%;margin-top:10px;">';

						pageList += '<div class="right" style="float:right">';

						//页始button
						pageList += '<a href="javascript:;" class="pageHome"><span>' + this.options.home_btn_name + '</span></a>';
						pageList +='<a href="javascript:;" class="pagePrev"><span>' + this.options.prev_btn_name + '</span></a>';
						
					pageList = this.pageGap(pageData, pageList);
					
					//页末button
					pageList +='<a href="javascript:;" class="pageNext"><span>' + this.options.next_btn_name + '</span></a>';
					pageList +='<a href="javascript:;" class="pageEnd"><span>' + this.options.end_btn_name + '</span></a>';
					
					pageList += '</div></div>';
					if($('.'+this.pageListExtendsNode()))
						$('.'+this.pageListExtendsNode()).remove();
					
					//$((this.options.tableNode)).after(pageList);
					$((this.options.tableNode)).parent().after(pageList);
					
					//刷新样式
					$('.'+this.pageListExtendsNode()).css({'float':'right'});
					
					$('.'+this.pageListExtendsNode()+ ' a').css({'border-top':'1px solid gray', 'border-right':'1px solid gray', 'border-bottom':'1px solid gray', 'padding':'10px 12px'});
					$('.'+this.pageListExtendsNode()+ ' a').eq(0).css({'border-left':'1px solid gray'});
					
					$('.'+this.pageListExtendsNode() +' .mjPageCurrent').css({'background-color':'#337ab7', 'color':'white'});
					
					
					var g_this = this;//移交控制
					//添加事件
					$('.'+this.pageListExtendsNode()+' .pageCenter').click(function(){
						var page = $(this).find('span').text();
						g_this.gotoPage(page, pageData);
					})
					//尾页
					$('.'+this.pageListExtendsNode()+' .pageEnd').click(function(){
						g_this.gotoPage(pageData.total_page, pageData);
					})
					//首页
					$('.'+this.pageListExtendsNode()+' .pageHome').click(function(){
						g_this.gotoPage(1, pageData);
					})
					//上一页
					$('.'+this.pageListExtendsNode()+' .pagePrev').click(function(){
						var prevPage = parseInt(pageData.current_page) - parseInt(1);
						var page = prevPage >0 ? prevPage : pageData.current_page;
						g_this.gotoPage(page, pageData);
					})
					//下一页
					$('.'+this.pageListExtendsNode()+' .pageNext').click(function(){
						var nextPage = parseInt(pageData.current_page) + parseInt(1);
						var page = nextPage > pageData.total_page ? pageData.total_page : nextPage;
						g_this.gotoPage(page, pageData);
					})
					
					this.jumpToPage(pageData);

					this.pageSelect();

					this.disable_bf_page_btn(pageData.current_page, pageData.total_page, function(){
						if(typeof callback==='function')
            				callback();
					});
					
				}else{
					console.warn('error: pageData.total_page not found!');
				}
			}
		},
		//根据条件禁用部分page button
		disable_bf_page_btn : function(current_page, max_page, callback){
			if(this.options.repeat_page===false){
				if(current_page==1){
					$('.'+this.pageListExtendsNode()+' .pageHome').css({'cursor':'not-allowed'});
					$('.'+this.pageListExtendsNode()+' .pagePrev').css({'cursor':'not-allowed'});
				}
				if(current_page==max_page){
					$('.'+this.pageListExtendsNode()+' .pageNext').css({'cursor':'not-allowed'});
					$('.'+this.pageListExtendsNode()+' .pageEnd').css({'cursor':'not-allowed'});
				}
				$('.'+this.pageListExtendsNode()+'  .mjPageCurrent').css({'cursor':'not-allowed'});

				if(typeof callback==='function')
    				callback();
			}
		},
		//跳页
		jumpToPage : function (pageData){
			if(this.options.onJumpPage===true){
				var goPageNode = '<span class="gotopagebox">Go:<input type="text" value="" class="GoPage_'+this.pageListExtendsNode()+'" />&nbsp;&nbsp;'+this.options.page+'/'+pageData.total_page+'</span>';
				$('.' + this.pageListExtendsNode() + ' .right').prepend(goPageNode);

				$('.gotopagebox').css({'margin-right':'10px'});
				
				var curToPageInputNode = '.GoPage_'+this.pageListExtendsNode();
				$(curToPageInputNode).css({'width':'45px', 'text-align':'center'});
				var g_this = this;
				
				$(curToPageInputNode).unbind('keydown');//避免重复绑定
				
				$(curToPageInputNode).keydown(function(e){
					if(e.keyCode===13){
						var toPage = $('.GoPage_'+g_this.pageListExtendsNode()).val();
							toPage = parseInt(toPage);
						if(toPage && toPage <= pageData.total_page){
							g_this.gotoPage(toPage, pageData);
						}else{
							console.warn('页码格式错误!');
						}
					}
				})
			}
		},
		//点击页码跳转
		gotoPage : function(page, pageData){
			//禁止重复翻页
			if(this.options.repeat_page===false)
				if(page==this.options.page)
					return false;

			if(parseInt(page) && pageData){
				this.options.page = page;
				
				this.drawSearch(this.options.searchNode, false);//翻页组织传递查询数据
				
				this.generate(this.options.server_url);
			}else{
				console.warn('error: gotoPage params not enough!');
			}
		},
		//page 页码 及 间隔规则计算
		pageGap : function(pageData, pageList){
			if(parseInt(pageData.total_page) && parseInt(pageData.current_page) && pageList){
				//默认页码数
				if(this.options.page_default_list > pageData.total_page)
					total_page = pageData.total_page;
				else
					total_page = this.options.page_default_list;
				
				var start, end;
					calc_start = parseInt(pageData.current_page) - parseInt(this.options.page_gap);
					calc_end = parseInt(pageData.current_page) + parseInt(this.options.page_gap);
					
					start = calc_start > 0 ? calc_start : 1;
					end = calc_end < this.options.page_default_list ? this.options.page_default_list : calc_end;
					end = end > pageData.total_page ? pageData.total_page : end;

					
					if(pageData.total_page > this.options.page_default_list && end-start<this.options.page_default_list){
						start = parseInt(end - this.options.page_default_list)+parseInt(1);
					}
					if(pageData.total_page < this.options.page_default_list && end-start < this.options.page_default_list){
						start = 1;
					}
					
				for(var p=start;p<=end;p++){
					var _mark = 'mjPageCommon';
					if(pageData.current_page==p)
						_mark = 'mjPageCurrent';
					
					pageList += '<a href="javascript:;" class="pageCenter '+ _mark +'"><span>' + p +'</span></a>';
				}
			}else{
				console.warn('error: calculate pageGap params not enough!');
			}
			return pageList;
		},
		//搜索api
		drawSearch : function(Node, markType, callback){
			markType = typeof arguments[1]==='undefined' ? true : markType;
			if(markType===true)
				this.options.page = 1;//复位至第一页
			
			if(Node && typeof Node==='function'){
				callback = Node;
				Node = null;
			}
			if(!Node)
				Node = this.options.searchNode;
			else
				this.options.searchNode = Node;
			
			if(this.options.searchNode){
				var len = $(this.options.searchNode).length;

				this.options.paramData = {};//重置参数
				
				/* orderColumn start	*/
				var tempYsortColumn = {};
				$('th[ysort]').each(function(){
					var sortField = $(this).attr('field');
					tempYsortColumn[sortField] = $(this).attr('ysort');
					MJDT.setParamData('orderColumn', tempYsortColumn);
				});
				/*	orderColumn	end		*/
				
				/* page_select start	*/
				if(this.options.page_select){
					var page_select = $('.'+this.pageListExtendsNode() + ' .pageListPageSelect select').val();
					if(page_select)
						this.options.page_size = page_select;
				}
				/* page_select end	*/
				for(var _l=0;_l<len;_l++){
					$(this.options.searchNode).eq(_l).addClass(this.formListExtendsNode() + '_search_form_len_' + _l);
					this.building(_l, markType, callback);
				}
			}
			return this;
		},
		//组织提交数据
		building : function(len, markType, callback){
			var current_searchNode = '.' +this.formListExtendsNode() + '_search_form_len_' + len;
			var paramData = $(current_searchNode).serialize();
				//paramData = decodeURIComponent(paramData,true);//解码中文问题

			if(paramData){
				var first = paramData.split('&');

				for(var k in first){
					inputData = first[k].split('=');
					if(inputData.length==2){
						
						var temp_item_val = decodeURIComponent(inputData[1].replace(/\+/g, " "), true);
						
						if(this.options.paramData[inputData[0]]){
							var temp = this.options.paramData[inputData[0]];
							this.options.paramData[inputData[0]] = new Array();
							//特殊  例:checkbox
							if(typeof temp==='string'){
								this.options.paramData[inputData[0]].push(temp, temp_item_val);
							}else if(typeof temp==='object'){
								//this.options.paramData[inputData[0]].push(temp.split(','), temp_item_val);
								temp.push(temp_item_val);
								this.options.paramData[inputData[0]] = temp;
							}
						}else{
							this.options.paramData[inputData[0]] = temp_item_val;
						}
					}
				}
			}
			
			if(markType===true)
				this.generate(null, callback);
			else
				if(typeof callback==='function')
					callback();
		},
		//列表渲染完毕 收尾事宜
		after : function(){
			$('.showFullContent').each(function(){
				var showLength = parseInt($(this).attr('showLength'));
				if(showLength){
					var fullContent = $(this).text();

					$(this).next('.fullContent').remove();
					if(fullContent.length > showLength)
						content = fullContent.substr(0, showLength) + '...';
					else
						content = fullContent;
					
					$(this).text(content);
					$(this).attr('title', fullContent);
					
					$(this).after('<span class="fullContent" style="display:none">'+fullContent+'</span>');
				}
			});
			
			$(this.options.tableNode).css({'word-break':'break-all'});
		},
		//js 重置样式
		controStyle : function(obj, callback){
			//style对象处理
			if(obj && typeof obj==='object')
				this.options.globalStyle = obj;
			
			if(typeof this.options.globalStyle==='object'){
				for(var m in this.options.globalStyle){
					$(m).css(this.options.globalStyle[m]);
				}
			}
			
			//回调处理
			if(typeof callback==='function')
				this.options.globalStyle.callbackfunction = callback;
			
			if(typeof this.options.globalStyle.callbackfunction==='function')
				this.options.globalStyle.callbackfunction();
		},
		//调取开启第一列box控制列   【强制开启】
		enableFirstControl : function(){
			this.options.first_control_columns = true;//开启
			this.first_control_columns();
		},
		//第一列控制列		【取决于配置】
		first_control_columns : function(){
			if(this.options.first_control_columns==true){
				if($(this.options.tableNode + ' .first_control_columns').length)
					$(this.options.tableNode + ' .first_control_columns').remove();
				
				$(this.options.tableNode + ' tr').prepend('<td class="first_control_columns first_control_td"><input type="checkbox" value="" name="first_control_box" /></td>');
				
				var k = 0;
				var span_node = this.options.tableNode + ' .first_control_td';
				$(span_node).each(function(){
					var _set_first_control = $(this).parent().find('set_first_control').text();
					$(this).parent().find('set_first_control').css('display', 'none');
					
					var _get = $(this).next().text();
					
					var inpNode = $(this).find('input');
					if(k!=0){
						inpNode.val(_set_first_control || _get);
						//单选
						$(inpNode).click(function(){
							var first = $(span_node + ' input').eq(0);

							var total_len = $(span_node + ' input').length-1,
								checked_len = $(span_node + " input[type='checkbox']:checked").length;
							
							if(total_len==checked_len && !$(span_node + ' input').eq(0).prop('checked'))
								$(span_node + ' input').eq(0).prop('checked', true);
							else
								$(span_node + ' input').eq(0).prop('checked', false);
						});
					}else{
						//全选 、全部选
						inpNode.click(function(){
							$(span_node + ' input').prop('checked', $(this).prop("checked"));
						})
					}
					k++;
				})
				$(span_node).css({'min-width':'10px'});
			}
		},
		//返回控制列数据
		//return array
		get_first_control_data : function(){
			if(this.options.first_control_columns==true){
				var control_data = new Array();
				$(this.options.tableNode + ' .first_control_td input[type="checkbox"]:checked').each(function(){
					if($(this).val())
						control_data.push($(this).val());
				})
				this.options.paramData.first_control_data = control_data;
				return control_data;
			}
		},
		
		//set paramData
		setParamData : function(key, value){
			if(key && value)
				this.options.paramData[key] = value;
			
			return this;
		},
		//遮罩
		mask_table : function(type){
			if(type=='close'){
				$('.mask_table_div_temp').remove();
				return;
			}else if(type=='open'){
				if(this.options.maskTable!==false){
					var curMaskWidth = $(this.options.tableNode).width();
					var curMaskHeight = $(this.options.tableNode).height();
					
					var m =document.createElement("div");
					m.style.width = curMaskWidth + 'px';
					m.style.height= curMaskHeight + 'px';
					m.className = 'mask_table_div_temp';
					m.innerHTML = '<span style="font-weight:bold;opacity:1;z-index:999;">加载中...</span>';
					m.style.textAlign = 'center';
					m.style.lineHeight = curMaskHeight + 'px';
					m.style.backgroundColor = 'white';
					//m.style.position = 'relative';
					m.style.marginTop = '-'+parseInt(curMaskHeight)+'px';
					//m.style.left = '0px';
					m.style.opacity = '0.6';
					m.style.fontSize = '20px';
					m.style.display = 'none';
					
					$(this.options.tableNode).after(m);
					
					m.style.display = 'block';
				}
				
				if(typeof this.options.maskTable==='function')
					this.options.maskTable();
			}
		},
		getStyle : function(element, styleName){
			if(element && styleName){
				var res = '';
				if(typeof element==='object' && typeof styleName==='string'){
					if(element.style[styleName]){//style
						res = element.style[styleName];
					}else if(element.currentStyle){//IE
			            res = element.currentStyle[styleName];
			        }else if(document.defaultView && document.defaultView.getComputedStyle){//DOM
			        	var ElementAttrs = document.defaultView.getComputedStyle(element, null);
			        	res = ElementAttrs.getPropertyValue(styleName);
			        }
				}
				return res;
			}
		},
		//回车搜索
		default_onsubmit : function(){
			var g_this = this;
			
			$(this.options.searchNode).unbind('submit');
			
			if(this.options.onReturnSubmit===true){
				$(this.options.searchNode).submit(function(){
					g_this.drawSearch(g_this.options.searchNode);
					return false;
				})
			}else if(this.options.onReturnSubmit===false){
				$(this.options.searchNode).submit(function(){
					console.warn('return submit is disable!');
					return false;
				})
			}
		},
		
		/**switch table 切换 datatable**/
		switchDataTable : function(tableNode, callback){
			var res = false;
			if(tableNode){
				this.options.tableNode = tableNode;
				var key = 'init_options_' + this.currentTableNode();

				if(default_clone_options[key]){
					for(var def_i in default_clone_options[key]){
						this.options.def_i = default_clone_options[key][def_i];
					}
					
					this.init(default_clone_options[key]);//初始化
					
					this.options.page = $('.'+this.pageListExtendsNode()+'  .mjPageCurrent').text();
					res=true;
				}
			}else{
				alert('switch error!');
			}
			if(typeof callback==='function')
				callback(res);
			else
				return this;
		},
		
		//clone options	backup
		clone_options : function(){
			var key = 'init_options_' + this.currentTableNode();
			default_clone_options[key] = new Object();
			for(var t in this.options){
				default_clone_options[key][t] = this.options[t];
			}
		}
};

var default_clone_options = {};
