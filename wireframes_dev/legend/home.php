/**
 * Add markers and notes in json format.
 * API:
 *  item.sel:  jQuery selector for item to which marker will be inserted.
 *  item.idx:  Label to display in marker and notes.
 *  item.styles: specify css rules to apply to marker. Define rules as object
 *     properties, eg., {left:'10px', top:'10px'}.  This is helpful when the
 *     default positioning isn't optimal, for example because it obscures
 *     important information.
 *  item.targetParent: Attach label to the parent DOM object (this is
 *     necessary when the main object can't contain child elements, as with <img>
 *  item.parentStyles: css declarations, as with item.styles, but applied to
 *     the parent DOM object (this can be necessary in instances where the
 *     element causes the marker to display incorrectly, perhaps because of
 *     overflow rules.
 *  item.notes:  Explanatory notes.  If desired, wrap a title for the note in
 *     a span with class="name".
 */
/*
elms: [
  {sel:'#masthead',idx:1,
    notes:'<span class="name">Masthead Region</span>'
  },
  {sel:'.search-bar', idx:2, styles:{left:'-28px'},
    notes:'<span class="name">Search Bar</span>'
  },
  {sel:'.social-media-buttons', idx:3, styles:{left:'-28px'}, parentStyles:{overflow:'visible'},
    notes:'<span class="name">Social Media</span>'
  },
  {sel:'.top-bar', idx:4, styles:{top:'-12px',left:'0'},
    notes:'<span class="name">Main Navigation</span>'
  },
  {sel:'.page-footer', idx:5,
    notes:'Page Footer'
  }
]*/
