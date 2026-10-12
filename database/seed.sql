SET NAMES utf8mb4;
INSERT INTO games (slug,name,category,currency,color,is_hot,needs_zone,sort_order) VALUES
 ('mobile-legends','Mobile Legends','Game Mobile','Diamond','#C7D2FE',1,1,1),
 ('free-fire','Free Fire','Game Mobile','Diamond','#FDE68A',1,0,2),
 ('genshin-impact','Genshin Impact','Game Mobile','Genesis Crystal','#FBCFE8',0,0,3),
 ('pubg-mobile','PUBG Mobile','Game Mobile','UC','#BAE6FD',1,0,4),
 ('roblox','Roblox','Game PC','Robux','#BBF7D0',0,0,5),
 ('valorant','Valorant','Game PC','VP','#DDD6FE',0,0,6),
 ('steam-wallet','Steam Wallet','Voucher','IDR Voucher','#E2E8F0',0,0,7),
 ('google-play','Google Play','Voucher','Voucher','#D1FAE5',0,0,8);

INSERT INTO packs (game_id,qty,price,sort_order)
SELECT g.id, p.qty, p.price, p.o FROM games g JOIN (
 SELECT 'mobile-legends' s,'86' qty,20000 price,1 o UNION ALL SELECT 'mobile-legends','172',39000,2 UNION ALL SELECT 'mobile-legends','257',58000,3 UNION ALL SELECT 'mobile-legends','514',115000,4 UNION ALL SELECT 'mobile-legends','1050',230000,5
 UNION ALL SELECT 'free-fire','70',10000,1 UNION ALL SELECT 'free-fire','140',20000,2 UNION ALL SELECT 'free-fire','355',50000,3 UNION ALL SELECT 'free-fire','720',100000,4
 UNION ALL SELECT 'genshin-impact','60',16000,1 UNION ALL SELECT 'genshin-impact','300',79000,2 UNION ALL SELECT 'genshin-impact','980',249000,3 UNION ALL SELECT 'genshin-impact','1980',479000,4
 UNION ALL SELECT 'pubg-mobile','60',15000,1 UNION ALL SELECT 'pubg-mobile','325',75000,2 UNION ALL SELECT 'pubg-mobile','660',149000,3
 UNION ALL SELECT 'roblox','80',15000,1 UNION ALL SELECT 'roblox','400',75000,2 UNION ALL SELECT 'roblox','800',150000,3
 UNION ALL SELECT 'valorant','475',59000,1 UNION ALL SELECT 'valorant','1000',119000,2 UNION ALL SELECT 'valorant','2050',239000,3
 UNION ALL SELECT 'steam-wallet','12000',14000,1 UNION ALL SELECT 'steam-wallet','45000',50000,2 UNION ALL SELECT 'steam-wallet','120000',130000,3
 UNION ALL SELECT 'google-play','20000',22000,1 UNION ALL SELECT 'google-play','50000',53000,2 UNION ALL SELECT 'google-play','100000',104000,3
) p ON p.s = g.slug;
