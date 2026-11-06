## Theories Api

---  
Return json data about all theories with some content.

- #### URL

    1. /api/theories

- #### Method
  1.Get

- #### URL Params (header)

    1. #### Required:
        1. Authorization (jwt authorization token from python )
    2. #### Optional:
        1. None

- #### Data Params
    1. lcid
  2. icons_scale = [one of [S, M, L]] - required
  3. sub-chapter-id = list of ids of sub-chapters - required
  4. content = [one or both of [theory, summary]]- required
  5. web = optional [default = 0]

- #### Success Response
    1. Code: 200
    2. Content: look at "TheoriesApiResponse.json"
- #### Error Response
    1. Code: 400
    2. Content: "bad content"
  3. Content: "bad sub-chapter-id"
  4. Content: "bad lcid"
- #### Sample Call
    1. https://langly-api-development-development.azurewebsites.net/api/theories?lcid=en&icons_scale=S&sub-chapter-id=9,14,15&content=theory&web=0

- #### Notes