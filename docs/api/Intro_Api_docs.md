
``` json
{
 "name": null ("name of intro, i think, string"),
 "locked": null ("image then intro is locked, url"),
 "unlocked": null ("image then intro is unlocked, url"),
 "scenes": ("array of scenes of intro")[
  {
  "tags": ("array of tags of scene")[ ],
  "locked": null ("image then scene is locked, url"),
  "unlocked": null ("image then scene is unlocked, url"),
  "pages": ("array of pages of scene")[
   {
   "id": 585 ("id of page"),
   "characters": ("array of characters") [
    {
    "id": 3 ("id of character"),
    "name": "sanea" ("name of character"),
    "current": null ("it is for focus on character"),
    "position": 1 ("position of character"),
    "image": null ("image of character, url")
    }
   ],
   "background": "http://api.example.com/api/bg/uploads/qwezx" ("background of page, url"),
   "text": ("localised static structure of text in the page")[
    {
    "LCID": "en" ('code of language'),
    "value": "eqw" ("localised text")
    },
    {
    "LCID": "ru" ('code of language'),
    "value": null ("localised text")
    }
   ],
   "content_type": 0 ("type of content of this page"),
   "order": 0 ("order of page in his hierarchy"),
   "next": ("array of next pages info")[
    {
    "id": 593 ("id of next page"),
    "name": ("localised text of next frame, if it is choice, it means value of buttons ")[
     {
     "LCID": "en" ('code of language'),
     "value": "asd" ("localised text")
     },
     {
     "LCID": "ru" ('code of language'),
     "value": null ("localised text")
     }
    ],
    "type": 0 ("type of button, its always 0 for now")
    }
    ]
   }
  ]
 }
 ]
}
```


##  More info about

Types of pages:

1. line = 0;
2. action = 1;
3. notification = 2;
4. choice = 3;

Position of personage:

1. left = 0,
2. right = 1

LCID can be from more then 2 symbols (pt-BR)

If characters is null it comme in this format: 
 
```
"characters": [ null ],
```

If next is null, it means that it is last page:

```

"next": null

```