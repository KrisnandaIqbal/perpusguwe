import React from 'react';
import { type BookItem } from '../utils/libraryLogic';

interface BookCoverArtProps {
  book: Pick<
    BookItem,
    'titleHighlight' | 'titleItalic' | 'author' | 'ddcCode' | 'coverGradient' | 'coverAccent' | 'coverPattern'
  >;
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'banner';
  className?: string;
}

export const BookCoverArt: React.FC<BookCoverArtProps> = ({
  book,
  size = 'md',
  className = '',
}) => {
  const sizeClasses = {
    xs: 'w-8 h-11 rounded-md text-[7px]',
    sm: 'w-12 h-16 rounded-lg text-[9px]',
    md: 'w-28 h-40 rounded-xl text-xs',
    lg: 'w-36 h-52 rounded-2xl text-sm',
    banner: 'w-full h-full rounded-2xl text-base',
  }[size];

  return (
    <div
      className={`relative overflow-hidden bg-gradient-to-br ${book.coverGradient} text-white shadow-lg select-none shrink-0 border border-white/15 ${sizeClasses} ${className}`}
      aria-label={`Sampul buku ${book.titleHighlight} ${book.titleItalic}`}
    >
      {/* Book spine crease effect */}
      <div className="absolute inset-y-0 left-0 w-2.5 bg-gradient-to-r from-black/40 via-white/15 to-transparent z-10 pointer-events-none" />

      {/* Decorative SVG Pattern based on book.coverPattern */}
      <svg
        className="absolute inset-0 w-full h-full opacity-25 pointer-events-none"
        viewBox="0 0 200 280"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        {book.coverPattern === 'constellation' && (
          <>
            <circle cx="100" cy="115" r="55" stroke="currentColor" strokeWidth="1.2" strokeDasharray="4 4" />
            <circle cx="100" cy="115" r="32" stroke="currentColor" strokeWidth="1" />
            <path d="M100 45 L116 95 L165 115 L116 135 L100 185 L84 135 L35 115 L84 95 Z" stroke="currentColor" strokeWidth="1.5" />
            <circle cx="60" cy="60" r="2.5" fill="currentColor" />
            <circle cx="145" cy="68" r="2" fill="currentColor" />
            <circle cx="152" cy="175" r="3" fill="currentColor" />
            <circle cx="48" cy="168" r="2" fill="currentColor" />
          </>
        )}
        {book.coverPattern === 'geometric' && (
          <>
            <polygon points="100,35 165,150 35,150" stroke="currentColor" strokeWidth="1.5" />
            <polygon points="100,175 165,60 35,60" stroke="currentColor" strokeWidth="1.2" opacity="0.7" />
            <circle cx="100" cy="105" r="24" stroke="currentColor" strokeWidth="1.5" />
          </>
        )}
        {book.coverPattern === 'editorial' && (
          <>
            <rect x="24" y="28" width="152" height="224" rx="8" stroke="currentColor" strokeWidth="1.5" />
            <circle cx="100" cy="115" r="42" stroke="currentColor" strokeWidth="2.5" />
            <circle cx="100" cy="115" r="16" fill="currentColor" />
            <line x1="24" y1="115" x2="176" y2="115" stroke="currentColor" strokeWidth="1.2" />
          </>
        )}
        {book.coverPattern === 'botanical' && (
          <>
            <path d="M100 220 C100 140 40 120 40 60 C100 60 100 120 100 220 Z" stroke="currentColor" strokeWidth="1.5" />
            <path d="M100 220 C100 140 160 120 160 60 C100 60 100 120 100 220 Z" stroke="currentColor" strokeWidth="1.5" />
            <circle cx="100" cy="80" r="18" stroke="currentColor" strokeWidth="1.2" />
          </>
        )}
        {book.coverPattern === 'archival' && (
          <>
            <path d="M20 50 Q100 20 180 50 L180 230 Q100 200 20 230 Z" stroke="currentColor" strokeWidth="1.5" />
            <line x1="45" y1="85" x2="155" y2="85" stroke="currentColor" strokeWidth="1" />
            <line x1="45" y1="115" x2="155" y2="115" stroke="currentColor" strokeWidth="1" />
            <line x1="45" y1="145" x2="125" y2="145" stroke="currentColor" strokeWidth="1" />
          </>
        )}
        {book.coverPattern === 'cyber' && (
          <>
            <rect x="35" y="45" width="130" height="130" rx="14" stroke="currentColor" strokeWidth="1.5" />
            <path d="M35 90 H165 M80 45 V175 M120 45 V175" stroke="currentColor" strokeWidth="1" strokeDasharray="3 3" />
            <circle cx="80" cy="90" r="6" fill="currentColor" />
            <circle cx="120" cy="130" r="6" fill="currentColor" />
          </>
        )}
      </svg>

      {/* Ambient glow */}
      <div
        className="absolute -top-8 -right-8 w-24 h-24 rounded-full blur-xl opacity-50 pointer-events-none"
        style={{ backgroundColor: book.coverAccent || '#F59E0B' }}
      />

      {/* Content typography */}
      {size !== 'xs' && size !== 'sm' ? (
        <div className="relative z-10 h-full flex flex-col justify-between p-3.5">
          <div className="flex items-center justify-between gap-1 text-[10px] font-mono-tabular text-white/80">
            <span className="truncate">{book.ddcCode.split(' ')[0]}</span>
            <span className="w-1.5 h-1.5 rounded-full bg-white/70 shrink-0" />
          </div>

          <div className="my-auto py-1">
            <p className="font-serif-editorial font-bold leading-tight tracking-tight text-white drop-shadow-sm line-clamp-2">
              {book.titleHighlight}
            </p>
            <p className="font-serif-editorial italic text-white/90 text-[0.85em] leading-snug line-clamp-2 mt-0.5">
              {book.titleItalic}
            </p>
          </div>

          <div className="pt-1.5 border-t border-white/20 flex items-center justify-between">
            <span className="text-[10px] font-medium text-white/85 truncate">
              {book.author}
            </span>
          </div>
        </div>
      ) : (
        <div className="relative z-10 h-full flex flex-col items-center justify-center p-1 text-center">
          <span className="font-serif-editorial font-bold leading-none tracking-tighter line-clamp-2 text-white">
            {book.titleHighlight.slice(0, 12)}
          </span>
        </div>
      )}
    </div>
  );
};
