# 给MongoDB增加auto_increment功能
#
# 使用：
#	db.users.insert(
#		{
#		    _id: getNextSequence("userid"),
#			name: "My Name"
#		}
#	)
function getNextSequence(name) {
	var ret = db.counters.findAndModify(
		{
			query: { _id: name },
			update: { $inc: { seq: 1 } },
			new: true,
			upsert: true
		}
	);

	return ret.seq;
}
