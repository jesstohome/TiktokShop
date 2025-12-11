import Cookies from 'js-cookie'

const TokenKey = 'Admin-Token'

const ExpiresInKey = 'Admin-Expires-In'

const UserId = 'User_Id'

const LocalLang = 'Local_Lang'

export function getToken() {
  return Cookies.get(TokenKey)
}

export function setToken(token) {
  return Cookies.set(TokenKey, token, { expires: 3 })
}

export function removeToken() {
  return Cookies.remove(TokenKey)
}

export function getExpiresIn() {
  return Cookies.get(ExpiresInKey) || -1
}

export function setExpiresIn(time) {
  return Cookies.set(ExpiresInKey, time)
}

export function removeExpiresIn() {
  return Cookies.remove(ExpiresInKey)
}

export function setUserId(user_id) {
  return Cookies.set(UserId, user_id, { expires: 3 })
}

export function getUserId() {
  return Cookies.get(UserId)
}

export function removeUserId() {
  return Cookies.remove(UserId)
}

export function setLocalLang(lang) {
  return Cookies.set(LocalLang, JSON.stringify(lang), { expires: 3 })
}

export function getLocalLang() {
  const lang = Cookies.get(LocalLang)
  if (lang) {
    return JSON.parse(Cookies.get(LocalLang))
  }
  return lang
}

export function removeLocalLang() {
  return Cookies.remove(LocalLang)
}
