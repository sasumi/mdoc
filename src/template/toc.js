const resolveTocListFromDom = (dom, levelMaps = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) => {
	let allHeads = dom.querySelectorAll(levelMaps.join(','));
	let tocList = [];
	let serials = [];

	levelMaps.forEach(selector => {
		serials.push(Array.from(dom.querySelectorAll(selector)));
	});

	let calcLvl = (h) => {
		for(let i = 0; i < serials.length; i++){
			if(serials.includes(h)){
				return i;
			}
		}
	};

	allHeads.forEach(h => {
		tocList.push({
			text: h.innerText,
			refNode: h,
			level: calcLvl(h)
		})
	});
	return tocList;
}

const LVS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
let coll = [];
let con = document.getElementById('toc-con');
Array.from(con.querySelectorAll(LVS.join(','))).forEach(hn=>{
	let txt =
	coll.push(`<li><a href="#${id}">${txt}</a></li>`);
})


