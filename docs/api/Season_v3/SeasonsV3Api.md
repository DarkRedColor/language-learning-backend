## Season Api Response

---  

- #### URL
  - /api/v3/seasons

- #### Method
  - Get

- #### URL Params (header)

  1. #### Required: 
     1. Authorization (jwt authorization token from python )

  2. #### Optional:
     1. None

- #### Data Params
  1. lcid
  2. list -> list of ids of seasons. -1 for all seasons
  3. icons_scale = [one of [S, M, L]] - required

- #### Success Response
    1. Code: 200
    2. Content: look at "SeasonsApiResponse.json"
- #### Error Response
  1. Code: 400
  2. Content: "bad lcid"
- #### Sample Call
  1. https://langly-api-development-development.azurewebsites.net/api/v3/seasons?lcid=en&list=-1&icons_scale=M

- #### Notes