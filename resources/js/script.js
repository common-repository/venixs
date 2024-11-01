if (typeof window === 'object') {
  if(venixsConfigScope && venixsConfigScope.signature) {
    window.venixsVariables = { ... { sync: true, signature: venixsConfigScope.signature, localization: 'all' }};
    var o = document;

    var f = () => {
      var e = o.createElement('script');
      e.type = 'text/javascript';
      e.async = true;
      e.src = 'https://lib.venixs.com/widget.js';
      var x = o.getElementsByTagName('script')[0];
      x.parentNode.insertBefore(e, x);
    };

    ( () => {
      if (o.readyState === "complete") {
        f();
      } else {
        window.addEventListener('load', f, false);
      }
    })()
  }
}