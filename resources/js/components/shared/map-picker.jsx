import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { useEffect, useMemo, useRef } from 'react';
import { Circle, MapContainer, Marker, TileLayer, useMap, useMapEvents } from 'react-leaflet';

import { mapConfig } from '@/config/map';
import { cn } from '@/lib/utils';

// Ikon diberikan eksplisit: jalur ikon bawaan Leaflet rusak bila dibundel oleh Vite
const pinIcon = L.icon({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

const toPosition = (latitude, longitude) => {
    const lat = Number.parseFloat(latitude);
    const lng = Number.parseFloat(longitude);
    const valid = Number.isFinite(lat) && Number.isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180;

    return valid ? [lat, lng] : null;
};

const format = (value) => value.toFixed(6);

function ClickHandler({ onPick, disabled }) {
    useMapEvents({
        click(event) {
            if (disabled) return;
            const { lat, lng } = event.latlng.wrap(); // jaga bujur tetap di -180 sampai 180
            onPick(lat, lng);
        },
    });

    return null;
}

function MapSync({ position }) {
    const map = useMap();

    // Dialog beranimasi, jadi ukuran awal peta belum tepat: hitung ulang tiap ukuran berubah
    useEffect(() => {
        const observer = new ResizeObserver(() => map.invalidateSize());
        observer.observe(map.getContainer());

        return () => observer.disconnect();
    }, [map]);

    // Saat koordinat diketik atau diambil dari GPS, geser peta bila titik di luar area yang terlihat
    useEffect(() => {
        if (!position) return;

        if (!map.getBounds().pad(-0.1).contains(position)) {
            map.setView(position, Math.max(map.getZoom(), mapConfig.pointZoom));
        }
    }, [position?.[0], position?.[1]]);

    return null;
}

/**
 * Pemilih titik di peta.
 * @param {{ latitude, longitude, radius, onChange: ({latitude, longitude}) => void, disabled?: boolean, className?: string }} props
 */
export default function MapPicker({ latitude, longitude, radius, onChange, disabled = false, className }) {
    const position = useMemo(() => toPosition(latitude, longitude), [latitude, longitude]);
    const initial = useRef(position); // pusat awal hanya dibaca sekali oleh MapContainer
    const radiusM = Number(radius);

    const pick = (lat, lng) => onChange({ latitude: format(lat), longitude: format(lng) });

    const markerEvents = useMemo(
        () => ({
            dragend(event) {
                const { lat, lng } = event.target.getLatLng().wrap();
                pick(lat, lng);
            },
        }),
        [],
    );

    return (
        <div className={cn('overflow-hidden rounded-lg border', className)}>
            <MapContainer
                center={initial.current ?? mapConfig.defaultCenter}
                zoom={initial.current ? mapConfig.pointZoom : mapConfig.defaultZoom}
                minZoom={3}
                maxBounds={[[-90, -180], [90, 180]]}
                maxBoundsViscosity={1}
                className="relative z-0 h-64 w-full md:h-80"
            >
                <TileLayer attribution={mapConfig.attribution} url={mapConfig.tileUrl} maxZoom={19} />
                <ClickHandler onPick={pick} disabled={disabled} />
                <MapSync position={position} />

                {position && (
                    <>
                        <Marker position={position} icon={pinIcon} draggable={!disabled} eventHandlers={markerEvents} />
                        {radiusM > 0 && (
                            <Circle
                                center={position}
                                radius={radiusM}
                                pathOptions={{ color: '#2563eb', weight: 2, fillColor: '#3b82f6', fillOpacity: 0.2 }}
                            />
                        )}
                    </>
                )}
            </MapContainer>
        </div>
    );
}
