export async function api(path, { body, method = 'GET', signal } = {}) {
    const headers = { Accept: 'application/json' }
    if (method !== 'GET') headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content
    if (body && !(body instanceof FormData)) {
        headers['Content-Type'] = 'application/json'
        body = JSON.stringify(body)
    }
    let response
    try {
        response = await fetch(path, { headers, method, body, signal, credentials: 'same-origin' })
    } catch (err) {
        if (err?.name === 'AbortError') throw err
        throw new Error('Koneksi terputus. Periksa jaringan internet Anda lalu coba lagi.')
    }
    if (response.status === 204) return null
    const data = response.headers.get('content-type')?.includes('application/json') ? await response.json() : {}
    if (!response.ok || response.redirected) {
        const message = response.status === 419 ? 'Sesi habis. Muat ulang halaman lalu coba lagi.' :
            response.status === 403 ? 'Akun Anda tidak memiliki akses ke bagian ini.' :
            response.status === 429 ? 'Terlalu banyak permintaan. Tunggu sebentar lalu coba lagi.' :
            response.redirected || response.status === 401 ? 'Masuk lagi untuk melanjutkan.' :
            Object.values(data.errors ?? {}).flat()[0] || (response.status < 500 && data.message) || 'Permintaan gagal. Coba lagi.'
        const error = new Error(message)
        error.status = response.status
        error.errors = data.errors ?? null
        throw error
    }
    return data
}

export function query(path, params) {
    const search = new URLSearchParams()
    for (const [name, value] of Object.entries(params)) {
        if (Array.isArray(value)) value.forEach(item => search.append(name + '[]', item))
        else if (value !== '' && value !== null && value !== undefined && value !== false) search.set(name, value === true ? '1' : value)
    }
    return search.size ? path + '?' + search : path
}

export function locate() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) return reject(new Error('Browser ini tidak mendukung lokasi.'))
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => resolve({ lat: coords.latitude, lng: coords.longitude }),
            () => reject(new Error('Lokasi belum tersedia. Cek izin lokasi di browser.')),
            { timeout: 9000, maximumAge: 60000 },
        )
    })
}
