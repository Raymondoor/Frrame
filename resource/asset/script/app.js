const page = window.INDEX;
const modules = import.meta.glob('./page/**/*.js');
const path = `./page/${page}.js`;
if (modules[path]){
    await modules[path]();
}else{
    console.warn(`No page script found for: ${path}`);
}