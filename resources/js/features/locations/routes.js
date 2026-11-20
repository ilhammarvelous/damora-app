const base = '/lokasi-kantor';

export const locationRoutes = {
    index: base,
    store: base,
    update: (id) => `${base}/${id}`,
    status: (id) => `${base}/${id}/status`,
};
