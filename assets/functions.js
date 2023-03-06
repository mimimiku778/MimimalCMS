/**
 * Shorthand function for document.querySelector, which returns the first matching element within the document.
 *
 * @function $qS
 * @param {string} selector - The selector to use for finding the element.
 * @returns {Element|null} - The first matching element, or null if none is found.
 */
const $qS = document.querySelector.bind(document)


/**
 * Shorthand function for document.querySelectorAll, which returns a NodeList of all matching elements within the document.
 *
 * @function $qSA
 * @param {string} selector - The selector to use for finding the elements.
 * @returns {NodeList} - A NodeList of all matching elements.
 */
const $qSA = document.querySelectorAll.bind(document)

/**
 * Finds an element by its ID.
 *
 * @param {string} id - The ID of the element to find.
 * @returns {Element|null} - The matching element, or null if none is found.
 */
const byId = id => document.getElementById(id)

/**
 * Adds a click event listener to an element, if it exists.
 *
 * @param {Element|null} element - The element to add the listener to.
 * @param {Function} callback - The function to execute when the element is clicked.
 */
const addClick = (element, callback) => element && element.addEventListener('click', callback)

/**
 * Gets the value of a cookie with the given name, treated as JSON.
 *
 * @param {string} name - The name of the cookie to get.
 * @param {string} [key] - The key of the cookie value to get. If not provided, the entire cookie object will be returned.
 * @returns {any} The value of the specified key in the cookie, parsed as JSON. If the cookie does not exist or the value cannot be parsed as JSON, returns undefined.
 *
 * @example
 * // Gets the value of "myKey" from a cookie named "myCookieName".
 * const getMyCookie = getJsonCookie('myCookieName');
 * const myCookieValue = getMyCookie('myKey');
 * 
 * @example
 * // Gets a JSON object from a cookie.
 * const MyCookie = getJsonCookie('myCookieName')();
 */
const getJsonCookie = (name) => (key) => {
  const cookieRegex = new RegExp(`(^|;)\\s*${name}\\s*=\\s*([^;]+)`)
  const cookieMatch = document.cookie.match(cookieRegex)

  if (!cookieMatch) {
    console.error(`Error: Cookie ${name} not found.`)
    return undefined
  }

  let parsedCookieValue = {}
  try {
    const cookieValue = decodeURIComponent(cookieMatch[2])
    parsedCookieValue = JSON.parse(cookieValue)
  } catch (e) {
    console.error(`Error: ${name} cookie is not a valid JSON string.`, e)
    return undefined
  }

  if (!parsedCookieValue) {
    console.error(`Error: ${name} cookie is not a valid JSON string.`)
    return undefined
  }

  if (!key) {
    return parsedCookieValue
  }

  if (!parsedCookieValue[key]) {
    console.error(`Error: Key ${key} not found in cookie ${name}.`)
    return undefined
  }

  return parsedCookieValue[key]
}

/**
* Sets a cookie with the given key-value pair or object in JSON format.
*
* @param {string} name - The name to use for the cookie.
* @param {number|null} expiresSeconds - The number of seconds until the cookie expires or null for a session cookie.
* @param {(string|Object.<string, any>)} keyOrData - The key for the value to store in the cookie, or an object containing key-value pairs to store.
* If an object is passed, it will be stored as a JSON string.
* @param {*} [value] - The value to store in the cookie. Only used if the second argument is a string.
*
* @example
* // Sets a cookie named "myCookieName" with a key-value pair of "key: value" that expires in 10 minutes
* const setMyCookie = setJsonCookie('myCookieName', 600)
* setMyCookie('key1', 'value1')
* setMyCookie('key2', 'value2')
*
* @example
* // If an object is passed as the second argument, the cookie data will be replaced with the object.
* setJsonCookie('myCookieName')(600)({ key: 'value' })
*
* @example
* // Sets a cookie named "myCookieName" with a key-value pair of "key: { nestedKey: 'nestedValue' }" that expires in a day
* setJsonCookie('myCookieName')(3600 * 24)('key', { nestedKey: 'nestedValue' })
*
* @example
* // If expiresSeconds is null, the cookie will expire at the end of the session.
* setJsonCookie('myCookieName')(null)('key', 'value')
*/
const setJsonCookie = (name) => (expiresSeconds) => (keyOrData, value) => {
  if (!name) {
    console.error('Error: Cookie name is required.')
    return
  }

  let cookieData = getJsonCookie(name)() || {}

  if (typeof keyOrData === 'object') {
    cookieData = keyOrData
  } else if (keyOrData) {
    cookieData[keyOrData] = value
  } else {
    console.error('Error: Cookie key or data is required.')
    return
  }

  const encodedData = encodeURIComponent(JSON.stringify(cookieData))

  let expires = ''
  if (expiresSeconds) {
    const expirationDate = new Date(Date.now() + expiresSeconds * 1000)
    expires = `;expires=${expirationDate.toUTCString()}`
  }

  const cookieString = `${name}=${encodedData}${expires}`
  document.cookie = cookieString
}

/**
* Deletes a JSON cookie by name and optional key.
* If key is provided, only the key-value pair corresponding to the key is deleted.
* If key is not provided, the entire cookie is deleted.
*
* @param {string} name - The name of the cookie to delete.
* @returns {void}
*
* @example
* // Deletes the entire cookie named "myCookieName"
* deleteJsonCookie("myCookieName")()
*
* @example
* // Deletes the key-value pair with key "myKey" in the cookie named "myCookieName"
* deleteJsonCookie("myCookieName")("myKey")
*/
const deleteJsonCookie = (name) => (key) => {
  if (!name) {
    console.error('Error: Cookie name is required.')
    return
  }

  const cookieString = document.cookie.match(`(^|;)\\s*${name}\\s*=\\s*([^;]+)`)
  if (!cookieString) {
    console.error(`Error: Cookie ${name} not found.`)
    return
  }

  let cookieData = {}
  try {
    cookieData = JSON.parse(decodeURIComponent(cookieString[2]))
  } catch (error) {
    console.error(`Error: parsing JSON from cookie ${name}.`, error)
    return
  }

  const getCookieExpires = (cookieString) => {
    const match = cookieString.match(/expires=([\w\d\s:,]+)/)
    if (match) {
      const expiresString = match[1]
      const expiresDate = new Date(expiresString)
      const nowDate = new Date()
      return Math.floor((expiresDate - nowDate) / 1000)
    }
    return null
  }

  const expiresSeconds = getCookieExpires(cookieString[0])

  if (key) {
    if (Object.prototype.hasOwnProperty.call(cookieData, key)) {
      delete cookieData[key]
      setJsonCookie(name)(expiresSeconds)(cookieData)
    } else {
      console.error(`Error: Key ${key} not found in cookie ${name}.`)
    }
  } else {
    setJsonCookie(name)(-1)({})
  }
}

/**
 * Sends a POST request to the specified URL with form data.
 *
 * @param {string} url - The URL to send the request to.
 * @param {Object|HTMLFormElement} [formData={}]
 *  The form data to include in the request, can be an object or an HTML form element.
 *  If an HTML form element is passed, the FormData will be generated from the form.
 * @param {function} [callback=null]
 *  An optional callback function to execute when the response is received.
 *  The function will be called with an object that has two properties:
 *  - data: The response data returned from the server.
 *  - code: The HTTP status code returned from the server.
 * 
 * @example
 * // Sending a POST request with an object as form data and logging the response
 * const formData = {
 *   name: 'John',
 *   email: 'john@example.com'
 * }
 * sendPostRequest('https://example.com/api', formData, ({ data, code }) => {
 *   console.log(Response data: ${data}, status code: ${code})
 * })
 * 
 * @example
 * // Sending a POST request with a form element as form data and displaying an alert
 * const form = document.querySelector('#myForm')
 * sendPostRequest('https://example.com/api', form, ({ data, code }) => {
 *   alert(Response data: ${data}, status code: ${code})
 * })
 */
const sendPostRequest = async (url, formData = {}, callback = null) => {
  let body
  if (formData instanceof HTMLFormElement) {
    body = new FormData(formData)
  } else if (formData instanceof Object) {
    body = new FormData()
    Object.entries(formData).forEach(([key, value]) => body.append(key, value))
  } else {
    console.error('Error: sendPostRequest: Invalid form data')
    return
  }

  try {
    const response = await fetch(url, { method: 'POST', body })
    const data = await response.json()
    if (callback) callback({ data, code: response.status })
  } catch (error) {
    console.error(error)
  }
}
