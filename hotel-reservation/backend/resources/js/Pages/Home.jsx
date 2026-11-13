import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Home({ rooms, roomTypes, filters = {} }) {
    const { errors } = usePage().props;
    const [search, setSearch] = useState({ arrival: filters.arrival || '', departure: filters.departure || '', type: filters.type || '', maxPrice: filters.maxPrice || '' });
    const [loading, setLoading] = useState(false);
    const update = e => setSearch({ ...search, [e.target.name]: e.target.value });
    return <MainLayout>
        <Head title="Chambres & séjours" />
        <section className="rounded-3xl bg-slate-900 px-6 py-12 text-stone-50 sm:px-12 sm:py-20">
            <p className="mb-4 text-xs uppercase tracking-[0.2em] text-amber-200">Prenez le temps de vous retrouver</p>
            <h1 className="max-w-2xl text-4xl font-semibold leading-tight sm:text-6xl">Votre prochain séjour commence ici.</h1>
            <p className="mt-6 max-w-xl text-stone-300">Explorez nos chambres et trouvez les disponibilités pour votre séjour.</p>
            <p className="mt-6 text-xs text-stone-400">Collection fictive · Application de démonstration</p>
        </section>
        <form className="relative mx-auto -mt-6 mb-12 grid max-w-5xl gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-lg sm:grid-cols-2 lg:grid-cols-5"
            onSubmit={e => { e.preventDefault(); router.get('/', search, { preserveState: true, onStart: () => setLoading(true), onFinish: () => setLoading(false) }); }}>
            <label className="text-sm font-medium">Arrivée<input name="arrival" type="date" value={search.arrival} onChange={update} className="mt-2 w-full rounded-lg border-stone-300" /></label>
            <label className="text-sm font-medium">Départ<input name="departure" type="date" min={search.arrival} value={search.departure} onChange={update} className="mt-2 w-full rounded-lg border-stone-300" /></label>
            <label className="text-sm font-medium">Type<select name="type" value={search.type} onChange={update} className="mt-2 w-full rounded-lg border-stone-300"><option value="">Tous les types</option>{roomTypes.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}</select></label>
            <label className="text-sm font-medium">Prix de base max. (€)<input name="maxPrice" type="number" min="0" placeholder="Sans limite" value={search.maxPrice} onChange={update} className="mt-2 w-full rounded-lg border-stone-300" /></label>
            <button disabled={loading} className="self-end rounded-lg bg-amber-700 px-4 py-3 font-medium text-white disabled:opacity-50">{loading ? 'Recherche…' : 'Rechercher'}</button>
            {Object.values(errors).map((error, i) => <p role="alert" key={i} className="text-sm text-red-700 sm:col-span-2 lg:col-span-5">{error}</p>)}
        </form>
        <div className="mb-6 flex flex-wrap items-end justify-between gap-3"><div><p className="text-xs uppercase tracking-widest text-amber-800">Notre collection</p><h2 className="mt-2 text-3xl font-semibold">Un séjour à votre image</h2></div><p className="text-sm text-stone-500">{rooms.length} chambre(s)</p></div>
        {rooms.length ? <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">{rooms.map(room => <article key={room.id} className="overflow-hidden rounded-2xl border border-stone-200 bg-white">
            {room.image ? <img className="h-52 w-full object-cover" src={room.image.startsWith('http') ? room.image : '/storage/' + room.image} alt={'Chambre ' + room.room_number} /> : <div className="flex h-52 items-center justify-center bg-gradient-to-br from-stone-200 to-amber-100 text-5xl text-stone-500" aria-label="Illustration typographique">{room.room_number}</div>}
            <div className="p-6"><p className="text-xs uppercase tracking-wider text-stone-500">Chambre {room.room_number} · {room.room_type.capacity} personne(s)</p><h3 className="mt-2 text-xl font-semibold">{room.room_type.name}</h3><p className="mt-3 min-h-12 text-sm text-stone-600">{room.room_type.description}</p><div className="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-4"><p><strong>{Number(room.room_type.base_price).toFixed(2)} €</strong><span className="text-xs text-stone-500"> / nuit, base</span></p><Link className="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white" href={'/chambres/' + room.id}>Voir la chambre</Link></div></div>
        </article>)}</div> : <div role="status" className="rounded-2xl border border-dashed border-stone-300 bg-white p-12 text-center"><h3 className="text-xl font-semibold">Aucune chambre disponible</h3><p className="mt-3 text-stone-600">Essayez d’autres dates ou élargissez vos critères.</p><Link href="/" className="mt-4 inline-block underline">Réinitialiser les filtres</Link></div>}
    </MainLayout>;
}
