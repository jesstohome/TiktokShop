export function regMobile(str) {
  return /^1[34578]\d{9}$/.test(str)
}

export function regEmail(str) {
  return /^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+\.[a-zA-Z]{2,4}$/.test(str)
}
