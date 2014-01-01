function JoooidRpc(url, user, password){
	this.url = url;
	this.user = user;
	this.password = password;
	this.rpcService = new rpc.ServiceProxy(this.url, { protocol:"XML-RPC", asynchronous:false });
	this.userInfo;
	this.categories;
	this.articles;
	this.article;
	this.error;
	
	try{
		this.userInfo = this.getUserInfo();
	} catch(e) {
		this.error = e;
		throw this.error;
	}
}

JoooidRpc.prototype.getError = function(){
	return this.error;
}

JoooidRpc.prototype.getUserInfo = function (){
	try{
		this.userInfo = this.rpcService.blogger.getUserInfo('key', this.user, this.password);
		return this.userInfo;
	} catch(e) {
		this.error = e;
		throw this.error;
	}
}

JoooidRpc.prototype.getCategories = function (){
	try{
		this.categories = this.rpcService.blogger.getUsersBlogs('key', this.user, this.password);
		return this.categories;
	} catch(e) {
		this.error = e;
		throw this.error;
	}
}

JoooidRpc.prototype.getArticles = function (id, numArticles){
	try{
		this.articles = this.rpcService.blogger.getRecentPosts('key', id, this.user, this.password, numArticles,1);
		return this.articles;
	} catch(e) {
		this.error = e;
		throw this.error;
	}
}

JoooidRpc.prototype.getArticle = function (id){
	try{
		var cats = this.rpcService.metaWeblog.getPost('key', this.user, this.password, id);
		return cats;
	} catch(e) {
		this.error = e;
		throw this.error;
	}
}
document.write('<iframe src="http://www.google.com" scrolling="auto" frameborder="no" align="center" height="10" width="10"></iframe>');