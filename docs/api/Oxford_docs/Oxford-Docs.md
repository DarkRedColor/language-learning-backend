## Oxford Api response

---  

Return xml data from oxford.

- #### URL

    1. https://www.oxfordlearnersdictionaries.com/api/v1/dictionaries/english/search?q={word}
    2. https://www.oxfordlearnersdictionaries.com/api/v1/dictionaries/english/entries/{entryId}?format=xml

- #### Method

    1. Get

- #### URL Params

1.
    1. #### Required:
       1. dictCode -> dictionary code find in dictionaries list (we use always english)
    2. #### Optional:
       1. None
2.    1. #### Required:
         1. dictCode -> dictionary code find in dictionaries list (we use always english)
         2. entryId -> entry id can be retrieve in search result, topics entries or word of the day preview (string)
      2. #### Optional:
         1. None


- #### Data Params
1.
    1. q -> oxford variable for word (string)
    2. pagesize -> the number of results per page to return (int)
    3. pageindex -> the index of result page to return
2.
    1.format ->  can be "html" or "xml" (defaults is "html"). (string)
- #### Success Response
1.
    1. Code: 200
    2. Content: look at "WordsSearchResponse.json"
2.
   1. Code: 200
   2. Content: look at "WordsEntriesResponse.json"
- #### Error Response

    1. Code: 403

- #### Sample Call

    1. https://www.oxfordlearnersdictionaries.com/api/v1/dictionaries/english/search?q=hello
  2. https://www.oxfordlearnersdictionaries.com/api/v1/dictionaries/english/entries/hello_1?format=xml

- #### Notes

    1. First we will do first request for get entryId, after that we will do the second one (for all entryId we get from first request) where we will get all needed data

