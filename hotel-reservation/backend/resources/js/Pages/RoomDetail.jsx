import { Head, Link, useForm, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function RoomDetail({ room }) {
    const { auth } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        room_id: room.id, guest_name: auth?.user?.name || '', check_in: '', check_out: '', guests: 1, request_key: crypto.randomUUID(),
    });
    const nights = data.check_in && data.check_out ? Math.max(0, Math.round((Date.parse(data.check_out) - Date.parse(data.check_in)) / 86400000)) : 0;
    const type = room.room_type;
    return <MainLayout><Head title={type.name} />
        <Link href="/" className="text-sm underline">← Toutes les chambres</Link>
        <div className="my-8 flex flex-wrap justify-between gap-4"><div><p className="text-xs uppercase tracking-widest text-amber-800">Chambre {room.room_number}</p><h1 className="mt-3 text-4xl font-semibold">{type.name}</h1></div><p className="text-xl"><strong>{Number(type.base_price).toFixed(2)} €</strong><span className="text-sm text-stone-500"> / nuit, tarif de base</span></p></div>
        {room.image && <img src={room.image.startsWith('http') ? room.image : '/storage/' + room.image} alt={type.name} className="mb-8 max-h-96 w-full rounded-2xl object-cover" />}
        {!!room.images?.length && <div className="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-3">{room.images.map(image => <img key={image.id} src={image.path.startsWith('http') ? image.path : '/storage/' + image.path} alt={'Vue de la chambre ' + room.room_number} className="h-40 w-full rounded-xl object-cover" />)}</div>}
        <div className="grid items-start gap-8 lg:grid-cols-2">
            <section className="rounded-2xl border border-stone-200 bg-white p-6"><h2 className="text-2xl font-semibold">Votre espace de tranquillité</h2><p className="mt-4 leading-relaxed text-stone-600">{type.description}</p><p className="mt-6 font-medium">Jusqu’à {type.capacity} personne(s)</p><p className="mt-4 text-sm text-stone-500">Annulation possible avant le jour d’arrivée, tant que la réservation est confirmée.</p><p className="mt-4 text-xs text-stone-500">Données et tarifs fictifs. Aucun paiement en ligne.</p></section>
            <form className="space-y-5 rounded-2xl border border-stone-200 bg-white p-5 sm:p-8" onSubmit={e => { e.preventDefault(); post('/reservations'); }}>
                <h2 className="text-2xl font-semibold">Réserver votre séjour</h2>
                <label className="block text-sm font-medium">Nom du voyageur<input required maxLength="255" value={data.guest_name} onChange={e => setData('guest_name', e.target.value)} className="mt-2 w-full rounded-lg border-stone-300" /></label>
                <div className="grid gap-4 sm:grid-cols-2">
                    <label className="text-sm font-medium">Arrivée<input required type="date" value={data.check_in} onChange={e => setData('check_in', e.target.value)} className="mt-2 w-full rounded-lg border-stone-300" /></label>
                    <label className="text-sm font-medium">Départ<input required type="date" min={data.check_in} value={data.check_out} onChange={e => setData('check_out', e.target.value)} className="mt-2 w-full rounded-lg border-stone-300" /></label>
                </div>
                <label className="block text-sm font-medium">Voyageurs<input required type="number" min="1" max={type.capacity} value={data.guests} onChange={e => setData('guests', e.target.value)} className="mt-2 w-full rounded-lg border-stone-300" /></label>
                {nights > 0 && <p className="rounded-lg bg-stone-100 p-4">{nights} nuit(s) · Estimation de base : {(nights * Number(type.base_price)).toFixed(2)} €<span className="mt-2 block text-xs text-stone-600">Le total définitif est calculé côté serveur avec le tarif applicable à chaque nuit.</span></p>}
                {Object.values(errors).map((error, i) => <p role="alert" key={i} className="text-sm text-red-700">{error}</p>)}
                {auth?.user ? <button disabled={processing || nights < 1 || room.status !== 'disponible'} className="w-full rounded-lg bg-slate-900 px-4 py-3 font-medium text-white disabled:opacity-50">{processing ? 'Réservation…' : 'Confirmer la réservation'}</button> : <Link href="/login" className="block rounded-lg bg-slate-900 px-4 py-3 text-center text-white">Se connecter pour réserver</Link>}
            </form>
        </div>
    </MainLayout>;
}
