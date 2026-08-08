// Match current route via defined const INDEX from AssetFacade
const page = window.INDEX;
const modules = import.meta.glob('./page/admin/**/*.js');
const path = `./page/admin/${page}.js`;
if (modules[path]){
    modules[path]();
}else{
    console.warn(`No page script found for: ${path}`);
}